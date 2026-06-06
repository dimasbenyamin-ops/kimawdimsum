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
        $tax        = round($subtotal * 0.11, 2);
        $grandTotal = round($subtotal + $tax, 2);
        $qrisImage  = Setting::getValue('qris_image');

        return view('customer.cart.index', compact('cart', 'subtotal', 'tax', 'grandTotal', 'qrisImage'));
    }

    public function add(Request $request): RedirectResponse
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

        return back()->with('success', e($menu->name) . ' ditambahkan ke keranjang.');
    }

    public function remove(Request $request, int $menuId): RedirectResponse
    {
        // Validate menuId is a positive integer (route model already handles this)
        if ($menuId <= 0) {
            abort(400);
        }

        $cart = $this->getCart();
        unset($cart[(string) $menuId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->route('menu.index')->with('success', 'Keranjang dikosongkan.');
    }

    private function getCart(): array
    {
        $raw = session('cart', []);
        // Guard: ensure cart is always an array (prevents session poisoning)
        return is_array($raw) ? $raw : [];
    }
}
