@extends('layouts.admin')

@section('title', 'Buat Stock Opname – Kumaw Dimsum Admin')

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
            <h1>📦 Buat Stock Opname</h1>
            <p>Masukkan hasil perhitungan fisik bahan baku di gudang.</p>
        </div>
        <a href="{{ route('admin.stock-opnames.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.stock-opnames.store') }}">
        @csrf

        <div class="form-card">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Informasi Stock Opname</h2>
            
            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem">
                <div class="form-group">
                    <label for="opname_date">Tanggal SO <span style="color:var(--error)">*</span></label>
                    <input type="date" id="opname_date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" required>
                    @error('opname_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Catatan Tambahan</label>
                    <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Catatan opsional..." maxlength="500">
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="form-card">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Daftar Bahan Baku</h2>
            <p style="color:var(--muted); margin-bottom:1.5rem; font-size:0.9rem">Isi "Stok Fisik" sesuai dengan perhitungan di lapangan. Kosongkan jika tidak ingin mengaudit bahan tersebut.</p>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:30%">Bahan Baku</th>
                            <th style="width:15%">Stok Sistem</th>
                            <th style="width:25%">Stok Fisik <span style="color:var(--error)">*</span></th>
                            <th style="width:30%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredients as $index => $ingredient)
                            <tr>
                                <td>
                                    <div style="font-weight: 500">{{ $ingredient->name }}</div>
                                    <input type="hidden" name="items[{{ $index }}][ingredient_id]" value="{{ $ingredient->id }}" class="item-id">
                                </td>
                                <td>
                                    <span style="font-family:monospace">{{ number_format($ingredient->current_stock, 4, ',', '.') }}</span>
                                    <span style="color:var(--muted); font-size:0.875rem">{{ $ingredient->unit->abbreviation ?? '' }}</span>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.5rem">
                                        <input type="number" name="items[{{ $index }}][physical_stock]" class="input-physical" step="any" min="0" placeholder="0" value="{{ $ingredient->current_stock }}" required style="width:120px">
                                        <span style="color:var(--muted); font-size:0.875rem">{{ $ingredient->unit->abbreviation ?? '' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $index }}][notes]" class="input-notes" placeholder="Alasan selisih..." style="width:100%">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display:flex;gap:0.75rem; margin-bottom:3rem">
            <button type="submit" class="btn btn-gold">
                <i class="bi bi-save"></i> Simpan Draft SO
            </button>
            <a href="{{ route('admin.stock-opnames.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
@endsection
