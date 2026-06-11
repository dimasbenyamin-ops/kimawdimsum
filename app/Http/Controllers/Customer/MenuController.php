<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    // Allow-list of valid category values
    private const VALID_CATEGORIES = ['original', 'spicy_mayo', 'goreng_keju', 'premium_sauce', 'sharing_party', 'snacks', 'minuman', 'add_on'];

    private const CATEGORY_LABELS = [
        'original'      => '🥟 Original',
        'spicy_mayo'    => '🌶️ Spicy Mayo',
        'goreng_keju'   => '🧀 Goreng Keju',
        'premium_sauce' => '🍯 Premium Sauce',
        'sharing_party' => '🎉 Sharing Party',
        'snacks'        => '🍟 Snacks',
        'minuman'       => '🍵 Minuman',
        'add_on'        => '➕ Add On',
    ];

    public function index(Request $request): View
    {
        // Validate category against allow-list (never trust user input for DB queries)
        $category = $request->query('category');
        if ($category && ! in_array($category, self::VALID_CATEGORIES, true)) {
            $category = null;
        }

        $query = Menu::available()->ordered();

        if ($category) {
            $query->byCategory($category);
        }

        $allMenus = $query->get();

        // Group by category for section display
        $menus = $category
            ? collect([($category) => $allMenus])
            : $allMenus->groupBy('category');

        $cart = session('cart', []);
        $cartCount = count($cart);

        return view('customer.menu.index', [
            'menus'          => $menus,
            'categoryLabels' => self::CATEGORY_LABELS,
            'activeCategory' => $category,
            'cartCount'      => $cartCount,
            'cart'           => $cart,
        ]);
    }
}
