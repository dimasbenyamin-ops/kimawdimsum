<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Purchase::with(['supplier', 'creator']);

        if ($search) {
            $query->where('purchase_number', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $purchases = $query->orderByDesc('purchase_date')
                           ->orderByDesc('id')
                           ->paginate(20)
                           ->withQueryString();

        return view('admin.purchases.index', compact('purchases', 'search', 'status'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::with('unit')->where('is_active', true)->orderBy('name')->get();

        return view('admin.purchases.create', compact('suppliers', 'ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'          => ['nullable', 'exists:suppliers,id'],
            'purchase_date'        => ['required', 'date'],
            'notes'                => ['nullable', 'string', 'max:500'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.ingredient_id'=> ['required', 'exists:ingredients,id'],
            'items.*.quantity'     => ['required', 'numeric', 'min:0.0001', 'max:999999'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0', 'max:999999999'],
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $purchaseItems = [];

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_price'];
                $totalAmount += $subtotal;

                // We need the unit_id from the ingredient
                $ingredient = Ingredient::find($itemData['ingredient_id']);

                $purchaseItems[] = new PurchaseItem([
                    'ingredient_id' => $itemData['ingredient_id'],
                    'quantity'      => $itemData['quantity'],
                    'unit_id'       => $ingredient->unit_id,
                    'unit_price'    => $itemData['unit_price'],
                    'subtotal'      => $subtotal,
                ]);
            }

            // Generate PO Number
            $datePrefix = date('ymd');
            $lastPurchase = Purchase::where('purchase_number', 'like', "PO-{$datePrefix}-%")->orderByDesc('id')->first();
            $sequence = 1;
            if ($lastPurchase) {
                $lastSequence = (int) substr($lastPurchase->purchase_number, -4);
                $sequence = $lastSequence + 1;
            }
            $purchaseNumber = "PO-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'supplier_id'     => $validated['supplier_id'],
                'purchase_date'   => $validated['purchase_date'],
                'total_amount'    => $totalAmount,
                'notes'           => $validated['notes'] ?? null,
                'status'          => 'draft',
                'created_by'      => auth()->id(),
            ]);

            $purchase->items()->saveMany($purchaseItems);

            DB::commit();

            return redirect()->route('admin.purchases.show', $purchase)
                             ->with('success', 'Pembelian berhasil dibuat sebagai Draft.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat pembelian: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pembelian. Silakan coba lagi.')->withInput();
        }
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['items.ingredient.unit', 'items.unit', 'supplier', 'creator']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
        ]);

        if ($purchase->status !== 'draft') {
            return back()->with('error', 'Hanya pembelian berstatus Draft yang dapat diubah statusnya.');
        }

        try {
            DB::beginTransaction();

            $purchase->status = $validated['status'];
            $purchase->save();

            if ($purchase->status === 'confirmed') {
                $this->processConfirmedPurchase($purchase);
            }

            DB::commit();

            $msg = $purchase->status === 'confirmed' ? 'Pembelian dikonfirmasi. Stok dan HPP telah diperbarui.' : 'Pembelian dibatalkan.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah status pembelian: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    private function processConfirmedPurchase(Purchase $purchase)
    {
        $purchase->load('items.ingredient');

        foreach ($purchase->items as $item) {
            $ingredient = $item->ingredient;

            // Update average cost using weighted average
            // new_avg = ((old_stock * old_avg) + (new_qty * new_price)) / (old_stock + new_qty)
            $oldStock = $ingredient->current_stock;
            $oldAvgCost = $ingredient->avg_cost;
            $newQty = $item->quantity;
            $newPrice = $item->unit_price;

            $newStock = $oldStock + $newQty;
            
            $newAvgCost = 0;
            if ($newStock > 0) {
                $oldTotalValue = $oldStock * $oldAvgCost;
                $newTotalValue = $newQty * $newPrice;
                $newAvgCost = ($oldTotalValue + $newTotalValue) / $newStock;
            } else {
                $newAvgCost = $newPrice; // Fallback
            }

            // Update ingredient
            $ingredient->current_stock = $newStock;
            $ingredient->avg_cost = $newAvgCost;
            $ingredient->save();

            // Note: Since we haven't created ingredient_stock_movements yet, we just update the models.
            // If we want audit trails, we should insert them here.
        }
    }
}
