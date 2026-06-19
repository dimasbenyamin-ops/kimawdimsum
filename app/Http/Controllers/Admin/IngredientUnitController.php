<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngredientUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * IngredientUnitController
 *
 * CRUD for measurement units used by ingredients (gram, kg, liter, pcs, etc.).
 * All inputs are validated with allow-lists and parameterized queries via Eloquent ORM.
 */
class IngredientUnitController extends Controller
{
    public function index(): View
    {
        $units = IngredientUnit::with('baseUnit')
            ->orderBy('name')
            ->get();

        return view('admin.ingredient-units.index', compact('units'));
    }

    public function create(): View
    {
        $baseUnits = IngredientUnit::whereNull('base_unit_id')
            ->orderBy('name')
            ->get();

        return view('admin.ingredient-units.create', compact('baseUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:50'],
            'abbreviation'      => ['required', 'string', 'max:10', 'unique:ingredient_units,abbreviation'],
            'base_unit_id'      => ['nullable', 'integer', 'exists:ingredient_units,id'],
            'conversion_factor' => ['required', 'numeric', 'min:0.000001', 'max:9999999999'],
        ]);

        IngredientUnit::create($validated);

        return redirect()
            ->route('admin.ingredient-units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(IngredientUnit $ingredientUnit): View
    {
        $baseUnits = IngredientUnit::whereNull('base_unit_id')
            ->where('id', '!=', $ingredientUnit->id)
            ->orderBy('name')
            ->get();

        return view('admin.ingredient-units.edit', [
            'unit'      => $ingredientUnit,
            'baseUnits' => $baseUnits,
        ]);
    }

    public function update(Request $request, IngredientUnit $ingredientUnit): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:50'],
            'abbreviation'      => ['required', 'string', 'max:10', Rule::unique('ingredient_units', 'abbreviation')->ignore($ingredientUnit->id)],
            'base_unit_id'      => ['nullable', 'integer', 'exists:ingredient_units,id'],
            'conversion_factor' => ['required', 'numeric', 'min:0.000001', 'max:9999999999'],
        ]);

        // Prevent self-reference
        if (isset($validated['base_unit_id']) && (int) $validated['base_unit_id'] === $ingredientUnit->id) {
            return back()->withErrors(['base_unit_id' => 'Satuan tidak boleh merujuk ke dirinya sendiri.']);
        }

        $ingredientUnit->update($validated);

        return redirect()
            ->route('admin.ingredient-units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(IngredientUnit $ingredientUnit): RedirectResponse
    {
        // Prevent deletion if used by ingredients
        if ($ingredientUnit->ingredients()->exists()) {
            return back()->with('error', 'Satuan ini masih digunakan oleh bahan baku dan tidak bisa dihapus.');
        }

        // Prevent deletion if used as base by other units
        if ($ingredientUnit->derivedUnits()->exists()) {
            return back()->with('error', 'Satuan ini masih menjadi acuan konversi satuan lain.');
        }

        $ingredientUnit->delete();

        return redirect()
            ->route('admin.ingredient-units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
