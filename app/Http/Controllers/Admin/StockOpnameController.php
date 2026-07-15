<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = StockOpname::with('creator');

        if ($search) {
            $query->where('opname_number', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $opnames = $query->orderByDesc('opname_date')
                         ->orderByDesc('id')
                         ->paginate(20)
                         ->withQueryString();

        return view('admin.stock-opnames.index', compact('opnames', 'search', 'status'));
    }

    public function create(): View
    {
        // Load all active ingredients for the physical count
        $ingredients = Ingredient::with('unit')->where('is_active', true)->orderBy('name')->get();

        return view('admin.stock-opnames.create', compact('ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opname_date'             => ['required', 'date'],
            'notes'                   => ['nullable', 'string', 'max:500'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.ingredient_id'   => ['required', 'exists:ingredients,id'],
            'items.*.physical_stock'  => ['required', 'numeric', 'min:0', 'max:999999'],
            'items.*.notes'           => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $datePrefix = date('ymd');
            $lastOpname = StockOpname::where('opname_number', 'like', "SO-{$datePrefix}-%")->orderByDesc('id')->first();
            $sequence = 1;
            if ($lastOpname) {
                $lastSequence = (int) substr($lastOpname->opname_number, -4);
                $sequence = $lastSequence + 1;
            }
            $opnameNumber = "SO-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $opname = StockOpname::create([
                'opname_number' => $opnameNumber,
                'opname_date'   => $validated['opname_date'],
                'notes'         => $validated['notes'] ?? null,
                'status'        => 'draft',
                'created_by'    => auth()->id(),
            ]);

            $opnameItems = [];
            foreach ($validated['items'] as $itemData) {
                $ingredient = Ingredient::find($itemData['ingredient_id']);
                $systemStock = $ingredient->current_stock;
                $physicalStock = $itemData['physical_stock'];
                
                $variance = $physicalStock - $systemStock;
                $varianceCost = $variance * $ingredient->avg_cost;

                $opnameItems[] = new StockOpnameItem([
                    'ingredient_id'  => $itemData['ingredient_id'],
                    'system_stock'   => $systemStock,
                    'physical_stock' => $physicalStock,
                    'variance'       => $variance,
                    'variance_cost'  => $varianceCost,
                    'notes'          => $itemData['notes'] ?? null,
                ]);
            }

            $opname->items()->saveMany($opnameItems);

            DB::commit();

            return redirect()->route('admin.stock-opnames.show', $opname)
                             ->with('success', 'Draft Stock Opname berhasil dibuat. Silakan tinjau variance sebelum konfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat stock opname: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat Stock Opname. Silakan coba lagi.')->withInput();
        }
    }

    public function show(StockOpname $stockOpname): View
    {
        $stockOpname->load(['items.ingredient.unit', 'creator']);

        return view('admin.stock-opnames.show', compact('stockOpname'));
    }

    public function updateStatus(Request $request, StockOpname $stockOpname): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
        ]);

        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Hanya Stock Opname berstatus Draft yang dapat diubah statusnya.');
        }

        try {
            DB::beginTransaction();

            $stockOpname->status = $validated['status'];
            $stockOpname->save();

            if ($stockOpname->status === 'confirmed') {
                $this->processConfirmedOpname($stockOpname);
            }

            DB::commit();

            $msg = $stockOpname->status === 'confirmed' ? 'Stock Opname dikonfirmasi. Stok sistem telah disesuaikan dengan fisik.' : 'Stock Opname dibatalkan.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah status opname: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    private function processConfirmedOpname(StockOpname $stockOpname)
    {
        $stockOpname->load('items.ingredient');

        foreach ($stockOpname->items as $item) {
            // Update the system stock to match the physical stock
            $ingredient = $item->ingredient;
            $ingredient->current_stock = $item->physical_stock;
            $ingredient->save();

            // Note: Add movement log for audit trail if needed.
        }
    }
}
