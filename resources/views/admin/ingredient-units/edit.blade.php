@extends('layouts.admin')

@section('title', 'Edit Satuan')

@section('content')
<div class="page-header">
    <div>
        <h1>✏️ Edit Satuan</h1>
        <p>Perbarui: <strong>{{ e($unit->name) }} ({{ e($unit->abbreviation) }})</strong></p>
    </div>
    <a href="{{ route('admin.ingredient-units.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body" style="max-width:540px">
        <form method="POST" action="{{ route('admin.ingredient-units.update', $unit) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Satuan *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $unit->name) }}"
                       required maxlength="50">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="abbreviation">Singkatan *</label>
                <input type="text" id="abbreviation" name="abbreviation"
                       value="{{ old('abbreviation', $unit->abbreviation) }}"
                       required maxlength="10">
                @error('abbreviation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="base_unit_id">Satuan Dasar (opsional)</label>
                <select id="base_unit_id" name="base_unit_id">
                    <option value="">— Satuan ini ADALAH satuan dasar —</option>
                    @foreach($baseUnits as $base)
                        <option value="{{ $base->id }}" {{ old('base_unit_id', $unit->base_unit_id) == $base->id ? 'selected' : '' }}>
                            {{ e($base->name) }} ({{ e($base->abbreviation) }})
                        </option>
                    @endforeach
                </select>
                @error('base_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="conversion_factor">Faktor Konversi *</label>
                <input type="number" id="conversion_factor" name="conversion_factor"
                       value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                       required min="0.000001" step="any" max="9999999999">
                <div class="form-hint">Contoh: 1 kg = 1000 gram → isi 1000</div>
                @error('conversion_factor') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Perbarui
                </button>
                <a href="{{ route('admin.ingredient-units.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
