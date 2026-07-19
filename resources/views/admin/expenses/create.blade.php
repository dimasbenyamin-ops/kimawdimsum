@extends('layouts.admin')

@section('title', 'Catat Biaya Operasional – Kumaw X Atmosphr Admin')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        max-width: 600px;
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>💸 Catat Biaya Operasional</h1>
            <p>Masukkan pengeluaran di luar pembelian bahan baku.</p>
        </div>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.expenses.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label for="expense_date">Tanggal <span style="color:var(--error)">*</span></label>
                <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="expense_category_id">Kategori <span style="color:var(--error)">*</span></label>
                <select id="expense_category_id" name="expense_category_id" required style="width:100%">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="amount">Jumlah (Rp) <span style="color:var(--error)">*</span></label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0" required>
                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}" placeholder="Contoh: Bayar listrik bulan Mei" maxlength="500">
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top:2rem; display:flex; gap:0.75rem;">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Simpan Biaya
                </button>
            </div>
        </div>
    </form>
@endsection
