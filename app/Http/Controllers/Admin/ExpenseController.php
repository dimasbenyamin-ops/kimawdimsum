<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('expense_category_id');

        $query = Expense::with(['category', 'creator']);

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $expenses = $query->orderByDesc('expense_date')
                          ->orderByDesc('id')
                          ->paginate(20)
                          ->withQueryString();
                          
        $categories = ExpenseCategory::orderBy('name')->get();

        return view('admin.expenses.index', compact('expenses', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount'              => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'expense_date'        => ['required', 'date'],
            'description'         => ['nullable', 'string', 'max:500'],
        ]);

        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Biaya operasional berhasil dicatat.');
    }

    public function edit(Expense $expense): View
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount'              => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'expense_date'        => ['required', 'date'],
            'description'         => ['nullable', 'string', 'max:500'],
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Biaya operasional berhasil diperbarui.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Biaya operasional berhasil dihapus.');
    }
}
