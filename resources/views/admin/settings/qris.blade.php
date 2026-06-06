@extends('layouts.admin')

@section('title', 'Pengaturan QRIS – Kumaw Dimsum')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4 shadow-sm border-0 bg-dark text-white">
            <div class="card-header bg-dark border-bottom border-secondary">
                <h5 class="mb-0 text-warning"><i class="bi bi-qr-code"></i> Upload QRIS</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success bg-success text-white border-0">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger bg-danger text-white border-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.settings.qris.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="qris_image" class="form-label">Pilih Gambar QRIS (JPG/PNG, Max 2MB)</label>
                        <input type="file" class="form-control bg-dark text-white border-secondary" id="qris_image" name="qris_image" accept="image/jpeg,image/png" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-upload"></i> Upload</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-4 shadow-sm border-0 bg-dark text-white">
            <div class="card-header bg-dark border-bottom border-secondary">
                <h5 class="mb-0 text-warning"><i class="bi bi-image"></i> Preview QRIS Aktif</h5>
            </div>
            <div class="card-body text-center">
                @if($qrisImage)
                    <img src="{{ Storage::url($qrisImage) }}" alt="QRIS Aktif" class="img-fluid rounded" style="max-height: 400px; border: 2px solid var(--gold);">
                @else
                    <div class="alert alert-secondary bg-secondary text-white border-0">
                        Belum ada gambar QRIS yang diupload.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
