<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuAdminController extends Controller
{
    // Allow-list of valid category values
    private const VALID_CATEGORIES = ['bundling_hemat', 'original', 'spicy_mayo', 'goreng_keju', 'premium_sauce', 'sharing_party', 'snacks', 'minuman', 'add_on'];

    public const CATEGORY_LABELS = [
        'bundling_hemat' => '🏷️ Bundling Hemat',
        'original'      => '🥟 Original',
        'spicy_mayo'    => '🌶️ Spicy Mayo',
        'goreng_keju'   => '🧀 Goreng Keju',
        'premium_sauce' => '🍯 Premium Sauce',
        'sharing_party' => '🎉 Sharing Party',
        'snacks'        => '🍟 Snacks',
        'minuman'       => '🍵 Minuman',
        'add_on'        => '➕ Add On',
    ];

    public function index(): View
    {
        $menus = Menu::withTrashed(false)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.menus.index', [
            'menus'          => $menus,
            'categoryLabels' => self::CATEGORY_LABELS,
        ]);
    }

    public function create(): View
    {
        return view('admin.menus.create', [
            'categoryLabels' => self::CATEGORY_LABELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMenuRequest($request);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Security: Use safe extension instead of client provided extension
            $filename = Str::random(40) . '.' . $file->extension();
            $file->move(public_path('images/menus'), $filename);
            $imagePath = 'images/menus/' . $filename;
        }

        Menu::create([
            'name'         => $validated['name'],
            'slug'         => Str::slug($validated['name']) . '-' . Str::random(5),
            'description'  => $validated['description'] ?? null,
            'category'     => $validated['category'],
            'price'        => $validated['price'],
            'image_path'   => $imagePath,
            'is_available' => $request->boolean('is_available'),
            'sort_order'   => (int) ($validated['sort_order'] ?? 100),
            'badge'        => $validated['badge'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu "' . e($validated['name']) . '" berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        $menu->load('masterRecipes');
        
        $masterRecipes = \App\Models\MasterRecipe::orderBy('name')->get();

        return view('admin.menus.edit', [
            'menu'           => $menu,
            'masterRecipes'  => $masterRecipes,
            'categoryLabels' => self::CATEGORY_LABELS,
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $this->validateMenuRequest($request, $menu);

        $updateData = [
            'name'         => $validated['name'],
            'slug'         => Str::slug($validated['name']) . '-' . Str::random(5),
            'description'  => $validated['description'] ?? null,
            'category'     => $validated['category'],
            'price'        => $validated['price'],
            'is_available' => $request->boolean('is_available'),
            'sort_order'   => (int) ($validated['sort_order'] ?? 100),
            'badge'        => $validated['badge'] ?? null,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($menu->image_path) {
                if (Str::startsWith($menu->image_path, 'images/menus/')) {
                    $oldPath = public_path($menu->image_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                } elseif (Str::startsWith($menu->image_path, 'menus/')) {
                    Storage::disk('public')->delete($menu->image_path);
                }
            }

            $file = $request->file('image');
            // Security: Use safe extension instead of client provided extension
            $filename = Str::random(40) . '.' . $file->extension();
            $file->move(public_path('images/menus'), $filename);
            $updateData['image_path'] = 'images/menus/' . $filename;
        }

        $menu->update($updateData);

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu "' . e($validated['name']) . '" berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $name = $menu->name;

        // Soft delete so existing OrderItems retain the price snapshot
        $menu->delete();

        // Remove image file from storage
        if ($menu->image_path) {
            if (Str::startsWith($menu->image_path, 'images/menus/')) {
                $path = public_path($menu->image_path);
                if (file_exists($path)) {
                    unlink($path);
                }
            } elseif (Str::startsWith($menu->image_path, 'menus/')) {
                Storage::disk('public')->delete($menu->image_path);
            }
        }

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu "' . e($name) . '" berhasil dihapus.');
    }

    public function syncRecipe(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'master_recipes' => ['nullable', 'array'],
            'master_recipes.*.id' => ['required', 'exists:master_recipes,id'],
            'master_recipes.*.multiplier' => ['required', 'numeric', 'min:0.0001', 'max:999999'],
        ]);

        $syncData = [];
        if (!empty($validated['master_recipes'])) {
            foreach ($validated['master_recipes'] as $item) {
                // Ensure unique master recipes
                if (!isset($syncData[$item['id']])) {
                    $syncData[$item['id']] = ['multiplier' => $item['multiplier']];
                }
            }
        }

        $menu->masterRecipes()->sync($syncData);

        return redirect()->route('admin.menus.edit', $menu)
                         ->with('success', 'Master Resep berhasil diperbarui untuk menu ini.');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    private function validateMenuRequest(Request $request, ?Menu $menu = null): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'category'    => ['required', 'string', 'in:' . implode(',', self::VALID_CATEGORIES)],
            'price'       => ['required', 'numeric', 'min:0', 'max:9999999'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
            'badge'       => ['nullable', 'string', 'max:50'],
            // Image required only on create; optional on update
            'image'       => [
                $menu ? 'nullable' : 'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2 MB
            ],
        ]);
    }
}
