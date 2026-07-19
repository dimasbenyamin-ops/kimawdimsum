@extends('layouts.admin')

@section('title', 'Edit Kategori Biaya – Kumaw X Atmosphr Admin')

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
            <h1>🏷️ Edit Kategori Biaya</h1>
            <p>Ubah nama kategori biaya operasional.</p>
        </div>
        <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.expense-categories.update', $expenseCategory) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-group">
                <label for="name">Nama Kategori <span style="color:var(--error)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $expenseCategory->name) }}" required maxlength="255">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top:2rem; display:flex; gap:0.75rem;">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Perbarui Kategori
                </button>
            </div>
        </div>
    </form>
@endsection
