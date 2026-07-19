@extends('layouts.admin')

@section('title', 'Tambah Master Resep – Kumaw X Atmosphr Admin')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>📖 Tambah Master Resep</h1>
            <p>Buat resep dasar baru beserta komposisi bahan bakunya.</p>
        </div>
        <a href="{{ route('admin.master-recipes.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.master-recipes.store') }}">
        @csrf

        <div class="form-card">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Informasi Master Resep</h2>
            
            <div class="form-group">
                <label for="name">Nama Master Resep <span style="color:var(--error)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="Contoh: Dimsum Original (Per Biji)" required maxlength="100"
                       class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}"
                       placeholder="Contoh: Takaran bahan untuk 1 biji dimsum original" maxlength="255"
                       class="{{ $errors->has('description') ? 'is-invalid' : '' }}">
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-card">
            <div style="margin-bottom: 1.5rem">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Bahan Baku (BOM)</h2>
                <p style="color: var(--muted); font-size: 0.875rem;">Tambahkan bahan baku yang dibutuhkan untuk 1 takaran master resep ini.</p>
            </div>

            <div id="recipe-items-container">
                {{-- Dynamic rows will be added here --}}
                <div id="empty-state" style="text-align:center; padding: 2rem; color: var(--muted); background: var(--surface-hover); border-radius: var(--radius); margin-bottom: 1rem;">
                    Belum ada bahan baku ditambahkan.
                </div>
            </div>

            <button type="button" id="btn-add-ingredient" class="btn btn-ghost" style="width:100%; border: 1px dashed var(--border); margin-bottom: 1.5rem;">
                <i class="bi bi-plus-lg"></i> Tambah Bahan Baku
            </button>
        </div>

        <div style="display:flex;gap:0.75rem">
            <button type="submit" class="btn btn-gold">
                <i class="bi bi-save"></i> Simpan Master Resep
            </button>
            <a href="{{ route('admin.master-recipes.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 0;
            const container = document.getElementById('recipe-items-container');
            const btnAdd = document.getElementById('btn-add-ingredient');
            const emptyState = document.getElementById('empty-state');

            const template = `
                <div class="recipe-item" style="display:flex; gap:1rem; align-items:flex-end; margin-bottom: 1rem; background: var(--surface-hover); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="flex: 2">
                        <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Bahan Baku</label>
                        <select name="ingredients[__INDEX__][id]" class="ingredient-select" required style="width:100%">
                            <option value="">— Pilih Bahan —</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit->abbreviation }}">
                                    {{ $ingredient->name }} ({{ $ingredient->unit->abbreviation }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1">
                        <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Takaran</label>
                        <div style="display:flex; align-items:center; gap:0.5rem">
                            <input type="number" name="ingredients[__INDEX__][quantity]" step="any" min="0.0001" placeholder="0" required style="width:100%">
                            <span class="unit-label" style="color:var(--muted); font-size:0.875rem; min-width:30px">—</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom: 2px" title="Hapus Bahan">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;

            btnAdd.addEventListener('click', function() {
                if (emptyState) emptyState.style.display = 'none';
                
                const html = template.replace(/__INDEX__/g, itemIndex++);
                container.insertAdjacentHTML('beforeend', html);
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.recipe-item').remove();
                    if (container.querySelectorAll('.recipe-item').length === 0 && emptyState) {
                        emptyState.style.display = 'block';
                    }
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('ingredient-select') || e.target.tagName === 'SELECT') {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const unitLabel = e.target.closest('.recipe-item').querySelector('.unit-label');
                    if (unitLabel && selectedOption && selectedOption.dataset.unit) {
                        unitLabel.textContent = selectedOption.dataset.unit;
                    } else if (unitLabel) {
                        unitLabel.textContent = '—';
                    }
                }
            });
        });
    </script>
@endsection
