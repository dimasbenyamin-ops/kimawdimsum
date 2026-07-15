<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * IngredientController
 *
 * CRUD for raw ingredients / bahan baku.
 * All inputs validated with allow-lists; queries use Eloquent ORM (parameterized).
 */
class IngredientController extends Controller
{
    public function index(Request $request): View
    {
        $search   = $request->query('search');
        $category = $request->query('category');
        $filter   = $request->query('filter'); // 'low_stock', 'inactive'

        // Validate category against allow-list
        if ($category && ! in_array($category, Ingredient::VALID_CATEGORIES, true)) {
            $category = null;
        }

        $query = Ingredient::with('unit');

        if ($search) {
            $query->search($search);
        }

        if ($category) {
            $query->byCategory($category);
        }

        if ($filter === 'low_stock') {
            $query->active()->lowStock();
        } elseif ($filter === 'inactive') {
            $query->where('is_active', false);
        }

        $ingredients = $query->orderBy('name')->paginate(20)->withQueryString();

        // Summary counts
        $totalCount    = Ingredient::count();
        $lowStockCount = Ingredient::active()->lowStock()->count();
        $totalValue    = Ingredient::active()->get()->sum(fn ($i) => $i->stock_value);

        return view('admin.ingredients.index', compact(
            'ingredients', 'search', 'category', 'filter',
            'totalCount', 'lowStockCount', 'totalValue'
        ));
    }

    public function create(): View
    {
        $units = IngredientUnit::orderBy('name')->get();

        return view('admin.ingredients.create', compact('units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'sku'           => ['nullable', 'string', 'max:50', 'unique:ingredients,sku'],
            'unit_id'       => ['required', 'integer', 'exists:ingredient_units,id'],
            'category'      => ['required', 'string', Rule::in(Ingredient::VALID_CATEGORIES)],
            'min_stock'     => ['required', 'numeric', 'min:0', 'max:9999999'],
            'current_stock' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'avg_cost'      => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'is_active'     => ['sometimes', 'boolean'],
        ]);

        $validated['is_active']     = $request->boolean('is_active', true);
        $validated['current_stock'] = $validated['current_stock'] ?? 0;
        $validated['avg_cost']      = $validated['avg_cost'] ?? 0;

        Ingredient::create($validated);

        return redirect()
            ->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(Ingredient $ingredient): View
    {
        $units = IngredientUnit::orderBy('name')->get();

        return view('admin.ingredients.edit', compact('ingredient', 'units'));
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'sku'       => ['nullable', 'string', 'max:50', Rule::unique('ingredients', 'sku')->ignore($ingredient->id)],
            'unit_id'   => ['required', 'integer', 'exists:ingredient_units,id'],
            'category'  => ['required', 'string', Rule::in(Ingredient::VALID_CATEGORIES)],
            'min_stock' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Note: current_stock and avg_cost are NOT editable here —
        // they are managed by purchase/sale/waste events.
        $ingredient->update($validated);

        return redirect()
            ->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        // Soft-delete to preserve historical stock movement references
        $ingredient->delete();

        return redirect()
            ->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
