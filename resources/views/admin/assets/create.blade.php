@extends('layouts.admin')

@section('title', 'Catat Aset Baru – Kumaw X Atmosphr Admin')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        max-width: 800px;
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.assets.index') }}" style="color: var(--muted); text-decoration: none; font-size: 0.875rem; margin-bottom: 0.5rem; display: inline-block;">
                &larr; Kembali ke Manajemen Aset
            </a>
            <h1><i class="bi bi-plus-circle"></i> Catat Aset Baru</h1>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('admin.assets.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem">
                <label class="form-label">Nama Aset / Inventaris <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Piring Melamin Putih, Mesin Kasir" required>
                @error('name')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem">
                <div>
                    <label class="form-label">Tanggal Beli <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    @error('purchase_date')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Kondisi Barang <span style="color:var(--danger)">*</span></label>
                    <select name="condition" class="form-control" required>
                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Bagus (Good)</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                        <option value="lost" {{ old('condition') == 'lost' ? 'selected' : '' }}>Hilang (Lost)</option>
                    </select>
                    @error('condition')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem">
                <div>
                    <label class="form-label">Kuantitas (Qty) <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" required>
                    @error('quantity')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Harga Satuan (Rp) <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="price_per_item" class="form-control" value="{{ old('price_per_item', 0) }}" min="0" step="0.01" required>
                    @error('price_per_item')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom: 2rem">
                <label class="form-label">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Misal: Nomor garansi, merk barang, dll">{{ old('notes') }}</textarea>
                @error('notes')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:1rem; border-top: 1px solid var(--border); padding-top: 1.5rem">
                <a href="{{ route('admin.assets.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save2"></i> Simpan Aset Baru
                </button>
            </div>
        </form>
    </div>
@endsection
