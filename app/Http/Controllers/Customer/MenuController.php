<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    // Allow-list of valid category values (matches DB enum)
    private const VALID_CATEGORIES = ['siomay', 'hakau', 'lumpia', 'bao', 'shumai', 'minuman', 'lainnya'];

    private const CATEGORY_LABELS = [
        'siomay'  => '🥟 Siomay',
        'hakau'   => '🦐 Hakau',
        'lumpia'  => '🌯 Lumpia',
        'bao'     => '🫓 Bao',
        'shumai'  => '🍢 Shumai',
        'minuman' => '🍵 Minuman',
        'lainnya' => '✨ Lainnya',
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

        $cartCount = count(session('cart', []));

        return view('customer.menu.index', [
            'menus'          => $menus,
            'categoryLabels' => self::CATEGORY_LABELS,
            'activeCategory' => $category,
            'cartCount'      => $cartCount,
        ]);
    }
}
