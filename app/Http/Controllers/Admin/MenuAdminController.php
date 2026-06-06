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
            // Store in public disk under menus/ directory
            $imagePath = $request->file('image')->store('menus', 'public');
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
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu "' . e($validated['name']) . '" berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        return view('admin.menus.edit', [
            'menu'           => $menu,
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
        ];

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('menus', 'public');
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
            Storage::disk('public')->delete($menu->image_path);
        }

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu "' . e($name) . '" berhasil dihapus.');
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
