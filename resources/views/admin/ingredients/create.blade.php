@extends('layouts.admin')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="page-header">
    <div>
        <h1>➕ Tambah Bahan Baku</h1>
        <p>Masukkan data bahan baku baru</p>
    </div>
    <a href="{{ route('admin.ingredients.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:640px">
        <form method="POST" action="{{ route('admin.ingredients.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Bahan *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       required maxlength="100" placeholder="Misal: Tepung Terigu">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="sku">SKU / Kode (opsional)</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                       maxlength="50" placeholder="Misal: BB-001">
                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="category">Kategori *</label>
                    <select id="category" name="category" required>
                        @foreach(\App\Models\Ingredient::CATEGORY_LABELS as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="unit_id">Satuan *</label>
                    <select id="unit_id" name="unit_id" required>
                        <option value="">— Pilih Satuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ e($unit->name) }} ({{ e($unit->abbreviation) }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="min_stock">Stok Minimum *</label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', 0) }}"
                           required min="0" step="0.01" max="9999999" placeholder="0">
                    <div class="form-hint">Alert akan muncul jika stok di bawah angka ini</div>
                    @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="current_stock">Stok Awal</label>
                    <input type="number" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}"
                           min="0" step="0.01" max="9999999" placeholder="0">
                    <div class="form-hint">Stok fisik saat ini (opsional)</div>
                    @error('current_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="avg_cost">Biaya Rata-rata per Unit (Rp)</label>
                <input type="number" id="avg_cost" name="avg_cost" value="{{ old('avg_cost', 0) }}"
                       min="0" step="0.01" max="9999999" placeholder="0">
                <div class="form-hint">Akan otomatis dihitung setelah ada pembelian</div>
                @error('avg_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    Aktif (tampil di daftar)
                </label>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
