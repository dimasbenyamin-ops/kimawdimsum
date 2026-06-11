<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart       = $this->getCart();
        $subtotal   = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $tax        = 0;
        $grandTotal = $subtotal;
        $qrisImage  = Setting::getValue('qris_image');

        return view('customer.cart.index', compact('cart', 'subtotal', 'tax', 'grandTotal', 'qrisImage'));
    }

    public function add(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'menu_id'  => ['required', 'integer', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'notes'    => ['nullable', 'string', 'max:500'],
        ]);

        // Verify item is still available (not just trusting the form hidden field)
        $menu = Menu::available()->findOrFail($validated['menu_id']);

        $cart   = $this->getCart();
        $key    = (string) $menu->id;
        $newQty = ($cart[$key]['quantity'] ?? 0) + $validated['quantity'];

        $cart[$key] = [
            'menu_id'       => $menu->id,
            'menu_name'     => $menu->name,
            'menu_category' => $menu->category,
            'unit_price'    => (float) $menu->price, // Always refresh price from DB
            'quantity'      => min(99, $newQty),
            'notes'         => $validated['notes'] ?? null,
        ];

        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => e($menu->name) . ' ditambahkan ke keranjang.',
                'cart_count' => count($cart)
            ]);
        }

        return back()->with('success', e($menu->name) . ' ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $menuId): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $this->getCart();
        $key = (string) $menuId;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $validated['quantity'];
            session(['cart' => $cart]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'quantity' => $validated['quantity']
            ]);
        }

        return back();
    }

    public function remove(Request $request, int $menuId): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // Validate menuId is a positive integer (route model already handles this)
        if ($menuId <= 0) {
            abort(400);
        }

        $cart = $this->getCart();
        unset($cart[(string) $menuId]);
        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart)
            ]);
        }

        return back();
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->route('menu.index')->with('success', 'Keranjang dikosongkan.');
    }

    public function reorder(\App\Models\Order $order): RedirectResponse
    {
        // Must own the order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $cart = $this->getCart();
        $added = 0;
        $unavailable = 0;

        foreach ($order->items as $item) {
            $menu = Menu::find($item->menu_id);
            if ($menu && $menu->is_available) {
                $key = (string) $menu->id;
                $newQty = ($cart[$key]['quantity'] ?? 0) + $item->quantity;

                $cart[$key] = [
                    'menu_id'       => $menu->id,
                    'menu_name'     => $menu->name,
                    'menu_category' => $menu->category,
                    'unit_price'    => (float) $menu->price, // Refresh price
                    'quantity'      => min(99, $newQty),
                    'notes'         => $item->notes, // Carry over previous notes
                ];
                $added++;
            } else {
                $unavailable++;
            }
        }

        session(['cart' => $cart]);

        $message = "Berhasil menambahkan $added menu ke keranjang.";
        if ($unavailable > 0) {
            $message .= " ($unavailable menu sudah tidak tersedia).";
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    private function getCart(): array
    {
        $raw = session('cart', []);
        // Guard: ensure cart is always an array (prevents session poisoning)
        return is_array($raw) ? $raw : [];
    }
}
