@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="page-header">
    <div>
        <h1>✏️ Edit Supplier</h1>
        <p>Perbarui data: <strong>{{ e($supplier->name) }}</strong></p>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:640px">
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Supplier *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}"
                       required maxlength="100">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="contact_person">PIC / Kontak</label>
                    <input type="text" id="contact_person" name="contact_person"
                           value="{{ old('contact_person', $supplier->contact_person) }}"
                           maxlength="100">
                    @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"
                           maxlength="30">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea id="address" name="address" rows="3" maxlength="500">{{ old('address', $supplier->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="2" maxlength="1000">{{ old('notes', $supplier->notes) }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                    Aktif
                </label>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Perbarui
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
