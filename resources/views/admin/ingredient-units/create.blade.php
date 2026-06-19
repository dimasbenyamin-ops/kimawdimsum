@extends('layouts.admin')

@section('title', 'Tambah Satuan')

@section('content')
<div class="page-header">
    <div>
        <h1>➕ Tambah Satuan</h1>
        <p>Masukkan satuan baru untuk bahan baku</p>
    </div>
    <a href="{{ route('admin.ingredient-units.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:540px">
        <form method="POST" action="{{ route('admin.ingredient-units.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Satuan *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       required maxlength="50" placeholder="Misal: kilogram">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="abbreviation">Singkatan *</label>
                <input type="text" id="abbreviation" name="abbreviation" value="{{ old('abbreviation') }}"
                       required maxlength="10" placeholder="Misal: kg">
                @error('abbreviation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="base_unit_id">Satuan Dasar (opsional)</label>
                <select id="base_unit_id" name="base_unit_id">
                    <option value="">— Satuan ini ADALAH satuan dasar —</option>
                    @foreach($baseUnits as $base)
                        <option value="{{ $base->id }}" {{ old('base_unit_id') == $base->id ? 'selected' : '' }}>
                            {{ e($base->name) }} ({{ e($base->abbreviation) }})
                        </option>
                    @endforeach
                </select>
                <div class="form-hint">Kosongkan jika satuan ini adalah satuan dasar (contoh: gram, pcs)</div>
                @error('base_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="conversion_factor">Faktor Konversi *</label>
                <input type="number" id="conversion_factor" name="conversion_factor"
                       value="{{ old('conversion_factor', 1) }}"
                       required min="0.000001" step="any" max="9999999999">
                <div class="form-hint">Contoh: 1 kg = 1000 gram → isi 1000</div>
                @error('conversion_factor') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
                <a href="{{ route('admin.ingredient-units.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
