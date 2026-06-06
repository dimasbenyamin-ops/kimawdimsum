<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        if (Auth::check()) {
            // Authenticated staff: show their user-linked orders
            $orders = Order::where('user_id', Auth::id())
                ->with('items')
                ->latest()
                ->paginate(10);
        } else {
            // Guest: show orders stored in session (last order placed)
            $orderIds = session('order_ids', []);
            $orders   = Order::with('items')
                ->whereIn('id', $orderIds)
                ->latest()
                ->paginate(10);
        }

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        // Ownership check:
        // - For authenticated users: must match user_id
        // - For guests: order must be in their session order_ids list
        if (Auth::check()) {
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            $orderIds = session('order_ids', []);
            if (! in_array($order->id, $orderIds)) {
                abort(403);
            }
        }

        $order->load('items');

        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'  => ['required', 'string', 'max:100'],
            'phone_number'   => ['required', 'string', 'min:10', 'max:20'],
            'type'           => ['required', 'in:dine_in,takeaway,delivery'],
            'payment_method' => ['required', 'in:cash,qris'],
            'table_number'   => ['nullable', 'integer', 'min:1', 'max:999'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);
        if (! is_array($cart) || empty($cart)) {
            return redirect()->route('cart.index')
                             ->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        $orderId = null;

        try {
            DB::transaction(function () use ($cart, $validated, &$orderId) {
                $subtotal = collect($cart)->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
                $tax      = round($subtotal * 0.11, 2);
                $total    = round($subtotal + $tax, 2);

                $order = Order::create([
                    'order_number'    => $this->generateOrderNumber(),
                    'user_id'         => Auth::id(), // null for guests
                    'customer_name'   => $validated['customer_name'],
                    'phone_number'    => $validated['phone_number'],
                    'status'          => Order::STATUS_PENDING_PAYMENT,
                    'type'            => $validated['type'],
                    'subtotal'        => $subtotal,
                    'discount_amount' => 0,
                    'tax_amount'      => $tax,
                    'total_amount'    => $total,
                    'payment_method'  => $validated['payment_method'],
                    'table_number'    => $validated['table_number'] ?? null,
                    'customer_notes'  => $validated['customer_notes'] ?? null,
                ]);

                foreach ($cart as $item) {
                    $order->items()->create([
                        'menu_id'       => $item['menu_id'],
                        'menu_name'     => $item['menu_name'],
                        'menu_category' => $item['menu_category'],
                        'quantity'      => (int) $item['quantity'],
                        'unit_price'    => (float) $item['unit_price'],
                        'notes'         => $item['notes'] ?? null,
                    ]);
                }

                session()->forget('cart');

                // Track order IDs in session so guest can view their order history
                $existingIds = session('order_ids', []);
                $existingIds[] = $order->id;
                session(['order_ids' => array_unique($existingIds)]);

                $orderId = $order->id;
            });
        } catch (\Throwable $e) {
            // Log detailed error — show generic message to user (never expose DB errors)
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return redirect()->route('cart.index')
                             ->withErrors(['order' => 'Gagal membuat pesanan. Silakan coba lagi.']);
        }

        return redirect()->route('orders.show', $orderId)
                         ->with('success', 'Pesanan berhasil dibuat! Silakan tunggu konfirmasi kasir.');
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'KD-' . now()->format('ymd');
        // Use DB lock to prevent race conditions on concurrent orders
        $count = Order::whereDate('created_at', today())->lockForUpdate()->count() + 1;
        return $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
