@extends('layouts.admin')

@section('title', 'Edit Bahan Baku')

@section('content')
<div class="page-header">
    <div>
        <h1>✏️ Edit Bahan Baku</h1>
        <p>Perbarui data: <strong>{{ e($ingredient->name) }}</strong></p>
    </div>
    <a href="{{ route('admin.ingredients.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:640px">
        <form method="POST" action="{{ route('admin.ingredients.update', $ingredient) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Bahan *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $ingredient->name) }}"
                       required maxlength="100">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="sku">SKU / Kode (opsional)</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku', $ingredient->sku) }}"
                       maxlength="50" placeholder="Misal: BB-001">
                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="category">Kategori *</label>
                    <select id="category" name="category" required>
                        @foreach(\App\Models\Ingredient::CATEGORY_LABELS as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $ingredient->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="unit_id">Satuan *</label>
                    <select id="unit_id" name="unit_id" required>
                        <option value="">— Pilih Satuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $ingredient->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ e($unit->name) }} ({{ e($unit->abbreviation) }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="min_stock">Stok Minimum *</label>
                <input type="number" id="min_stock" name="min_stock"
                       value="{{ old('min_stock', $ingredient->min_stock) }}"
                       required min="0" step="0.01" max="9999999">
                <div class="form-hint">Alert akan muncul jika stok di bawah angka ini</div>
                @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Read-only stock info --}}
            <div style="background:var(--surface-hover);border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1.25rem">
                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.5rem">
                    Info Stok (Otomatis)
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                    <div>
                        <div style="font-size:0.75rem;color:var(--muted)">Stok Saat Ini</div>
                        <div style="font-weight:700;{{ $ingredient->isLowStock() ? 'color:var(--error)' : '' }}">
                            {{ $ingredient->formatted_stock }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;color:var(--muted)">Biaya Rata²</div>
                        <div style="font-weight:700">{{ $ingredient->formatted_avg_cost }}</div>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;color:var(--muted)">Nilai Stok</div>
                        <div style="font-weight:700;color:var(--gold)">Rp {{ number_format($ingredient->stock_value, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ingredient->is_active) ? 'checked' : '' }}>
                    Aktif (tampil di daftar)
                </label>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Perbarui
                </button>
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
