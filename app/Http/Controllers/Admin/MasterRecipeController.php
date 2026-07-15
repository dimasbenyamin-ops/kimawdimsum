<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\MasterRecipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterRecipeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = MasterRecipe::withCount('ingredients');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $masterRecipes = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.master-recipes.index', compact('masterRecipes', 'search'));
    }

    public function create(): View
    {
        $ingredients = Ingredient::with('unit')->orderBy('name')->get();

        return view('admin.master-recipes.create', compact('ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'description'            => ['nullable', 'string', 'max:255'],
            'ingredients'            => ['nullable', 'array'],
            'ingredients.*.id'       => ['required', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0.0001', 'max:999999'],
        ]);

        $masterRecipe = MasterRecipe::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['ingredients'])) {
            $syncData = [];
            foreach ($validated['ingredients'] as $item) {
                // Keep only the first instance if there are duplicates
                if (!isset($syncData[$item['id']])) {
                    $syncData[$item['id']] = ['quantity' => $item['quantity']];
                }
            }
            $masterRecipe->ingredients()->sync($syncData);
        }

        return redirect()->route('admin.master-recipes.index')
                         ->with('success', 'Master Resep berhasil ditambahkan.');
    }

    public function edit(MasterRecipe $masterRecipe): View
    {
        $masterRecipe->load('ingredients.unit');
        $ingredients = Ingredient::with('unit')->orderBy('name')->get();

        return view('admin.master-recipes.edit', compact('masterRecipe', 'ingredients'));
    }

    public function update(Request $request, MasterRecipe $masterRecipe): RedirectResponse
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'description'            => ['nullable', 'string', 'max:255'],
            'ingredients'            => ['nullable', 'array'],
            'ingredients.*.id'       => ['required', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0.0001', 'max:999999'],
        ]);

        $masterRecipe->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $syncData = [];
        if (!empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $item) {
                if (!isset($syncData[$item['id']])) {
                    $syncData[$item['id']] = ['quantity' => $item['quantity']];
                }
            }
        }
        $masterRecipe->ingredients()->sync($syncData);

        return redirect()->route('admin.master-recipes.index')
                         ->with('success', 'Master Resep berhasil diperbarui.');
    }

    public function destroy(MasterRecipe $masterRecipe): RedirectResponse
    {
        if ($masterRecipe->menus()->exists()) {
            return back()->with('error', 'Master Resep ini sedang digunakan oleh menu dan tidak dapat dihapus.');
        }

        $masterRecipe->delete();

        return redirect()->route('admin.master-recipes.index')
                         ->with('success', 'Master Resep berhasil dihapus.');
    }
}
