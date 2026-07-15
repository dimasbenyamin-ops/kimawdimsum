@extends('layouts.admin')

@section('title', 'Edit Menu Sidebar – Kumaw Dimsum Admin')

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
            <h1>🗂️ Edit Menu Sidebar</h1>
            <p>Ubah konfigurasi menu navigasi sidebar.</p>
        </div>
        <a href="{{ route('admin.nav-menus.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.nav-menus.update', $navMenu) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-group">
                <label for="name">Nama Menu <span style="color:var(--error)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $navMenu->name) }}" required maxlength="255">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="route_name">Route Name <span style="color:var(--error)">*</span></label>
                <input type="text" id="route_name" name="route_name" value="{{ old('route_name', $navMenu->route_name) }}" required maxlength="255">
                @error('route_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="icon">Ikon (Bootstrap Icons)</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', $navMenu->icon) }}" maxlength="255">
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="parent_id">Induk Menu (Parent)</label>
                <select id="parent_id" name="parent_id" class="form-control" style="width:100%">
                    <option value="">— Tidak Ada (Jadikan Menu Utama) —</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $navMenu->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">Urutan (Sort Order) <span style="color:var(--error)">*</span></label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $navMenu->sort_order) }}" required min="0">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top:2rem; display:flex; gap:0.75rem;">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Perbarui Menu
                </button>
            </div>
        </div>
    </form>
@endsection
