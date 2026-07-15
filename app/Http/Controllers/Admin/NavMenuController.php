<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavMenu;
use App\Models\Role;
use Illuminate\Http\Request;

class NavMenuController extends Controller
{
    public function index(Request $request)
    {
        $query = NavMenu::with('parent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('route_name', 'like', "%{$search}%");
            });
        }

        $navMenus = $query->orderBy('parent_id')
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.nav-menus.index', compact('navMenus'));
    }

    public function create()
    {
        $parents = NavMenu::whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.nav-menus.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'route_name' => 'required|string|max:255|unique:nav_menus,route_name',
            'icon'       => 'nullable|string|max:255',
            'parent_id'  => 'nullable|exists:nav_menus,id',
            'sort_order' => 'required|integer',
        ]);

        $navMenu = NavMenu::create($validated);

        // Secara otomatis tambahkan menu baru ke role Administrator (id 1) jika ada,
        // supaya tidak hilang/tidak bisa diakses setelah dibuat.
        $adminRole = Role::find(1);
        if ($adminRole) {
            $adminRole->navMenus()->attach($navMenu->id);
        }

        return redirect()->route('admin.nav-menus.index')->with('success', 'Menu sidebar berhasil dibuat dan otomatis diberikan ke Administrator.');
    }

    public function edit(NavMenu $navMenu)
    {
        $parents = NavMenu::whereNull('parent_id')
                          ->where('id', '!=', $navMenu->id)
                          ->orderBy('sort_order')->get();
        return view('admin.nav-menus.edit', compact('navMenu', 'parents'));
    }

    public function update(Request $request, NavMenu $navMenu)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'route_name' => 'required|string|max:255|unique:nav_menus,route_name,' . $navMenu->id,
            'icon'       => 'nullable|string|max:255',
            'parent_id'  => 'nullable|exists:nav_menus,id',
            'sort_order' => 'required|integer',
        ]);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $navMenu->id) {
            return back()->withErrors(['parent_id' => 'Menu tidak dapat menjadi induk untuk dirinya sendiri.'])->withInput();
        }

        $navMenu->update($validated);

        return redirect()->route('admin.nav-menus.index')->with('success', 'Menu sidebar berhasil diperbarui.');
    }

    public function destroy(NavMenu $navMenu)
    {
        if ($navMenu->children()->count() > 0) {
            return redirect()->route('admin.nav-menus.index')->with('error', 'Menu ini tidak dapat dihapus karena memiliki sub-menu.');
        }

        $navMenu->delete();

        return redirect()->route('admin.nav-menus.index')->with('success', 'Menu sidebar berhasil dihapus.');
    }
}
