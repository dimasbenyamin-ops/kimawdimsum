<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Show the cashier POS order form with all available menu items.
     */
    public function create()
    {
        // ponytail: check active shift
        if (!Shift::where('user_id', Auth::id())->where('status', 'open')->exists()) {
            return redirect()->route('cashier.shifts.create')->with('error', 'Silakan mulai shift terlebih dahulu.');
        }

        $menus = Menu::available()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('cashier.order', compact('menus'));
    }

    /**
     * Store a new order placed by the cashier on behalf of a customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'  => ['required', 'string', 'max:100'],
            'phone_number'   => ['nullable', 'string', 'min:8', 'max:20'],
            'type'           => ['required', 'in:dine_in,takeaway'],
            'payment_method' => ['required', 'in:cash,qris'],
            'table_number'   => ['nullable', 'integer', 'min:1', 'max:999'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.menu_id'  => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'    => ['nullable', 'string', 'max:255'],
            'discount_amount'  => ['nullable', 'integer', 'min:0'],
        ], [
            'items.required'        => 'Pilih minimal satu menu.',
            'items.min'             => 'Pilih minimal satu menu.',
            'items.*.menu_id.exists' => 'Menu tidak valid.',
        ]);

        try {
            $order = null;
            DB::transaction(function () use ($validated, &$order) {
                // Fetch menu items to get price snapshots
                $menuIds = collect($validated['items'])->pluck('menu_id')->unique();
                $menus   = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

                // Build line items and compute subtotal
                $lineItems = [];
                $subtotal  = 0;

                foreach ($validated['items'] as $item) {
                    $menu = $menus->get($item['menu_id']);
                    if (! $menu) {
                        continue;
                    }
                    $qty       = (int) $item['quantity'];
                    $price     = (float) $menu->price;
                    $subtotal += $price * $qty;

                    $lineItems[] = [
                        'menu_id'       => $menu->id,
                        'menu_name'     => $menu->name,
                        'menu_category' => $menu->category,
                        'quantity'      => $qty,
                        'unit_price'    => $price,
                        'notes'         => $item['notes'] ?? null,
                    ];
                }

                if (empty($lineItems)) {
                    throw new \RuntimeException('Tidak ada item yang valid dipilih.');
                }

                $discount = (float) ($validated['discount_amount'] ?? 0);
                $tax   = 0;
                $total = max(0, $subtotal - $discount + $tax);

                $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();

                // Cashier orders (Cash/QRIS EDC) go straight to confirmed & paid
                $order = Order::create([
                    'order_number'    => $this->generateOrderNumber(),
                    'shift_id'        => $activeShift?->id,
                    'user_id'         => null, // walk-in / cashier-created orders have no user account
                    'customer_name'   => $validated['customer_name'],
                    'phone_number'    => $validated['phone_number'] ?? null,
                    'status'          => Order::STATUS_CONFIRMED,
                    'type'            => $validated['type'],
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount'      => $tax,
                    'total_amount'    => $total,
                    'payment_method'  => $validated['payment_method'],
                    'paid_at'         => now(), // cashier verifies payment immediately

                    'table_number'    => $validated['table_number'] ?? null,
                    'customer_notes'  => $validated['customer_notes'] ?? null,
                    'processed_by'    => Auth::id(),
                ]);

                foreach ($lineItems as $line) {
                    $order->items()->create($line);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Cashier order creation failed', [
                'cashier_id' => Auth::id(),
                'error'      => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['order' => 'Gagal membuat pesanan: ' . e($e->getMessage())]);
        }

        // Both Cash and EDC QRIS go directly to receipt printing
        return redirect()
            ->route('cashier.orders.receipt', ['order' => $order->id])
            ->with('success', 'Pesanan Lunas! Silakan cetak struk.');
    }

    /**
     * Generate a unique order number: KD-YYMMDD-NNNN
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'KD-' . now()->format('ymd');
        $count  = Order::whereDate('created_at', today())->lockForUpdate()->count() + 1;

        return $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the receipt for printing.
     */
    public function receipt(Order $order): View
    {
        $order->load(['items', 'processor']);
        return view('cashier.receipt', compact('order'));
    }
}
