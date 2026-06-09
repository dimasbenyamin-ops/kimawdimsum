@extends('layouts.admin')

@section('title', 'Edit Menu: ' . $menu->name . ' – Kumaw Dimsum Admin')

@section('styles')
<style>
    .form-card {
        max-width: 640px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
    }
    .price-prefix {
        display: flex;
        align-items: center;
    }
    .price-prefix-label {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        border-right: none;
        border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        padding: 0.6875rem 0.875rem;
        color: var(--muted);
        font-size: 0.9375rem;
        white-space: nowrap;
    }
    .price-prefix input {
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .switch-row {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.625rem 0;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: rgba(255,255,255,0.1);
        border-radius: 24px;
        transition: 0.3s;
    }
    .slider:before {
        content: '';
        position: absolute;
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }
    input:checked + .slider { background: var(--gold); }
    input:checked + .slider:before { transform: translateX(20px); }

    .current-image {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        border-radius: 10px;
        margin-bottom: 0.75rem;
    }
    .current-image img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 8px;
        background: var(--bg2);
    }
    .current-image-info { font-size: 0.8rem; color: var(--muted); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>✏️ Edit Menu</h1>
            <p>{{ $menu->name }}</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Menu <span style="color:var(--error)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}"
                       placeholder="Contoh: Siomay Udang Premium" required maxlength="120"
                       class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="category">Kategori <span style="color:var(--error)">*</span></label>
                <select id="category" name="category" class="{{ $errors->has('category') ? 'is-invalid' : '' }}">
                    @foreach($categoryLabels as $value => $label)
                        <option value="{{ $value }}" {{ old('category', $menu->category) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="price">Harga (IDR) <span style="color:var(--error)">*</span></label>
                <div class="price-prefix">
                    <span class="price-prefix-label">Rp</span>
                    <input type="number" id="price" name="price" value="{{ old('price', (int)$menu->price) }}"
                           placeholder="15000" min="0" max="9999999" step="500" required
                           class="{{ $errors->has('price') ? 'is-invalid' : '' }}"
                           style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0">
                </div>
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" maxlength="500"
                          placeholder="Deskripsi singkat tentang menu ini..."
                          style="resize:vertical"
                          class="{{ $errors->has('description') ? 'is-invalid' : '' }}"
                >{{ old('description', $menu->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Foto Menu</label>
                @if($menu->image_path)
                    <div class="current-image">
                        <img src="{{ Str::startsWith($menu->image_path, 'images/') ? asset($menu->image_path) : asset('storage/' . $menu->image_path) }}" alt="{{ e($menu->name) }}">
                        <div>
                            <div style="font-size:0.875rem;font-weight:500;margin-bottom:0.25rem">Foto saat ini</div>
                            <div class="current-image-info">Upload foto baru untuk mengganti</div>
                        </div>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                       class="{{ $errors->has('image') ? 'is-invalid' : '' }}">
                <div style="font-size:0.8rem;color:var(--muted);margin-top:0.375rem">
                    Format: JPG, PNG, WebP. Maks. 2MB.
                </div>
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">Urutan Tampil</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $menu->sort_order) }}"
                       min="0" max="9999"
                       class="{{ $errors->has('sort_order') ? 'is-invalid' : '' }}">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <div class="switch-row">
                    <label class="switch" for="is_available">
                        <input type="checkbox" id="is_available" name="is_available" value="1"
                               {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <div>
                        <div style="font-size:0.9rem;font-weight:500">Tersedia untuk dipesan</div>
                        <div style="font-size:0.8rem;color:var(--muted)">Matikan untuk sembunyikan dari pelanggan</div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-gold" id="btn-update-menu">💾 Simpan Perubahan</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
