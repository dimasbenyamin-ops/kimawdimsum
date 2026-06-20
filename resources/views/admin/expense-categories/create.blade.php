@extends('layouts.admin')

@section('title', 'Tambah Kategori Biaya – Kumaw Dimsum Admin')

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
            <h1>🏷️ Tambah Kategori Biaya</h1>
            <p>Tambahkan kategori baru untuk biaya operasional.</p>
        </div>
        <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.expense-categories.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label for="name">Nama Kategori <span style="color:var(--error)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Tagihan Listrik" required maxlength="255">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top:2rem; display:flex; gap:0.75rem;">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Simpan Kategori
                </button>
            </div>
        </div>
    </form>
@endsection
