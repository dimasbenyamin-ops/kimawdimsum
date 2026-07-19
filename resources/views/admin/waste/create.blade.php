@extends('layouts.admin')

@section('title', 'Catat Waste Baru – Kumaw X Atmosphr Admin')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        max-width: 800px;
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🗑️ Catat Waste Baru</h1>
            <p>Masukkan data bahan baku yang rusak, basi, atau terbuang.</p>
        </div>
        <a href="{{ route('admin.waste.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.waste.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label for="waste_date">Tanggal <span style="color:var(--error)">*</span></label>
                <input type="date" id="waste_date" name="waste_date" value="{{ old('waste_date', date('Y-m-d')) }}" required>
                @error('waste_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="ingredient_id">Bahan Baku <span style="color:var(--error)">*</span></label>
                <select id="ingredient_id" name="ingredient_id" required style="width:100%">
                    <option value="">— Pilih Bahan Baku —</option>
                    @foreach($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit->abbreviation }}" data-stock="{{ $ingredient->current_stock }}" {{ old('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
                            {{ $ingredient->name }} (Stok saat ini: {{ number_format($ingredient->current_stock, 4, ',', '.') }} {{ $ingredient->unit->abbreviation }})
                        </option>
                    @endforeach
                </select>
                @error('ingredient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Kuantitas Terbuang <span style="color:var(--error)">*</span></label>
                <div style="display:flex; align-items:center; gap:0.5rem">
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" step="any" min="0.0001" placeholder="0" required style="width:100%">
                    <span id="unit-label" style="font-size:0.875rem; color:var(--muted); min-width:30px">—</span>
                </div>
                <small id="stock-warning" style="color:var(--danger); display:none; margin-top:0.5rem">Kuantitas melebihi stok yang tersedia!</small>
                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="reason">Alasan <span style="color:var(--error)">*</span></label>
                <select id="reason" name="reason" required style="width:100%">
                    <option value="">— Pilih Alasan —</option>
                    <option value="expired" {{ old('reason') === 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                    <option value="damaged" {{ old('reason') === 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                    <option value="sample" {{ old('reason') === 'sample' ? 'selected' : '' }}>Sampel / Tester</option>
                    <option value="production_loss" {{ old('reason') === 'production_loss' ? 'selected' : '' }}>Gagal Produksi / Jatuh</option>
                    <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Catatan Tambahan</label>
                <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Penjelasan lebih lanjut (opsional)..." maxlength="500">
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div style="margin-top:2rem; padding:1rem; background:var(--surface-hover); border-radius:var(--radius)">
                <i class="bi bi-info-circle" style="color:var(--gold); margin-right:0.5rem"></i>
                <span style="font-size:0.9rem; color:var(--muted)">Mencatat waste akan otomatis mengurangi stok bahan baku dan mencatat estimasi kerugian berdasarkan harga rata-rata (HPP) bahan.</span>
            </div>
        </div>

        <div style="display:flex;gap:0.75rem; margin-bottom:3rem">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash3"></i> Catat Waste
            </button>
            <a href="{{ route('admin.waste.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ingredientSelect = document.getElementById('ingredient_id');
            const qtyInput = document.getElementById('quantity');
            const unitLabel = document.getElementById('unit-label');
            const stockWarning = document.getElementById('stock-warning');

            const checkStock = () => {
                const selectedOption = ingredientSelect.options[ingredientSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const currentStock = parseFloat(selectedOption.dataset.stock) || 0;
                    const inputQty = parseFloat(qtyInput.value) || 0;
                    
                    unitLabel.textContent = selectedOption.dataset.unit;
                    
                    if (inputQty > currentStock) {
                        stockWarning.style.display = 'block';
                        qtyInput.style.borderColor = 'var(--danger)';
                    } else {
                        stockWarning.style.display = 'none';
                        qtyInput.style.borderColor = 'var(--border)';
                    }
                } else {
                    unitLabel.textContent = '—';
                    stockWarning.style.display = 'none';
                }
            };

            ingredientSelect.addEventListener('change', checkStock);
            qtyInput.addEventListener('input', checkStock);
            
            // Initial check if validation failed and old data exists
            if (ingredientSelect.value) {
                checkStock();
            }
        });
    </script>
@endsection
