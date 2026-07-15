@extends('layouts.admin')

@section('title', 'Tambah Supplier')

@section('content')
<div class="page-header">
    <div>
        <h1>➕ Tambah Supplier</h1>
        <p>Masukkan data supplier baru</p>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:640px">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Supplier *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       required maxlength="100" placeholder="Misal: CV Sumber Udang">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="contact_person">PIC / Kontak</label>
                    <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                           maxlength="100" placeholder="Nama penanggung jawab">
                    @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           maxlength="30" placeholder="08xxxxxxxxxx">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea id="address" name="address" rows="3" maxlength="500"
                          placeholder="Alamat lengkap supplier">{{ old('address') }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="2" maxlength="1000"
                          placeholder="Catatan internal...">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    Aktif
                </label>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
