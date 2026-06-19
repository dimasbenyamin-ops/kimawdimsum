@extends('layouts.admin')

@section('title', 'Identitas Toko')

@section('content')
<div class="page-header">
    <div>
        <h1>Identitas Toko</h1>
        <p>Kelola nama, alamat, dan nomor telepon yang akan dicetak di struk pelanggan.</p>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.settings.store_identity.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="store_name">Nama Toko</label>
                <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $storeName) }}" class="form-control @error('store_name') is-invalid @enderror" required>
                @error('store_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="store_address">Alamat Toko</label>
                <textarea id="store_address" name="store_address" rows="3" class="form-control @error('store_address') is-invalid @enderror" required>{{ old('store_address', $storeAddress) }}</textarea>
                @error('store_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="store_phone">Nomor Telepon</label>
                <input type="text" id="store_phone" name="store_phone" value="{{ old('store_phone', $storePhone) }}" class="form-control @error('store_phone') is-invalid @enderror" required>
                @error('store_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
