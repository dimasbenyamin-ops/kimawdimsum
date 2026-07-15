<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\WasteLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WasteLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $reason = $request->query('reason');

        $query = WasteLog::with(['ingredient.unit', 'creator']);

        if ($search) {
            $query->whereHas('ingredient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($reason) {
            $query->where('reason', $reason);
        }

        $wasteLogs = $query->orderByDesc('waste_date')
                           ->orderByDesc('id')
                           ->paginate(20)
                           ->withQueryString();

        return view('admin.waste.index', compact('wasteLogs', 'search', 'reason'));
    }

    public function create(): View
    {
        $ingredients = Ingredient::with('unit')->where('is_active', true)->orderBy('name')->get();

        return view('admin.waste.create', compact('ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity'      => ['required', 'numeric', 'min:0.0001', 'max:999999'],
            'reason'        => ['required', 'in:expired,damaged,sample,production_loss,other'],
            'waste_date'    => ['required', 'date'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $ingredient = Ingredient::find($validated['ingredient_id']);

            if ($ingredient->current_stock < $validated['quantity']) {
                return back()->with('error', 'Kuantitas waste melebihi stok yang tersedia.')->withInput();
            }

            $costAmount = $validated['quantity'] * $ingredient->avg_cost;

            WasteLog::create([
                'ingredient_id' => $validated['ingredient_id'],
                'quantity'      => $validated['quantity'],
                'unit_id'       => $ingredient->unit_id,
                'reason'        => $validated['reason'],
                'notes'         => $validated['notes'] ?? null,
                'waste_date'    => $validated['waste_date'],
                'cost_amount'   => $costAmount,
                'created_by'    => auth()->id(),
            ]);

            // Deduct stock
            $ingredient->decrement('current_stock', $validated['quantity']);

            DB::commit();

            return redirect()->route('admin.waste.index')
                             ->with('success', 'Data waste berhasil dicatat dan stok telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mencatat waste: ' . $e->getMessage());
            return back()->with('error', 'Gagal mencatat data waste. Silakan coba lagi.')->withInput();
        }
    }
}
