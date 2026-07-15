<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $assets = $query->orderBy('purchase_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalAssetValue = Asset::where('condition', 'good')->sum('total_price');

        return view('admin.assets.index', compact('assets', 'totalAssetValue'));
    }

    public function create()
    {
        return view('admin.assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'purchase_date'  => 'required|date',
            'quantity'       => 'required|integer|min:1',
            'price_per_item' => 'required|numeric|min:0',
            'condition'      => 'required|in:good,damaged,lost',
            'notes'          => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['price_per_item'];

        Asset::create($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Aset baru berhasil dicatat.');
    }

    public function edit(Asset $asset)
    {
        return view('admin.assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'purchase_date'  => 'required|date',
            'quantity'       => 'required|integer|min:0',
            'price_per_item' => 'required|numeric|min:0',
            'condition'      => 'required|in:good,damaged,lost',
            'notes'          => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['price_per_item'];

        $asset->update($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Data aset berhasil dihapus.');
    }
}
