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
                <label for="badge">Badge/Label (Opsional)</label>
                <input type="text" id="badge" name="badge" value="{{ old('badge', $menu->badge) }}"
                       placeholder="Contoh: Best Seller, Pedas 🔥, Rekomendasi" maxlength="50"
                       class="{{ $errors->has('badge') ? 'is-invalid' : '' }}">
                <div style="font-size:0.8rem;color:var(--muted);margin-top:0.375rem">
                    Teks ini akan muncul sebagai pita/label di foto menu untuk menarik perhatian.
                </div>
                @error('badge') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    {{-- Master Resep Section --}}
    <div class="form-card" style="margin-top: 2rem;">
        <div style="margin-bottom: 1.5rem">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">📝 Master Resep</h2>
            <p style="color: var(--muted); font-size: 0.875rem;">Pilih master resep dan tentukan jumlah pengali (contoh: isi 4 pcs = pengali 4).</p>
        </div>

        <form method="POST" action="{{ route('admin.menus.syncRecipe', $menu) }}">
            @csrf

            <div id="recipe-items-container">
                @forelse($menu->masterRecipes as $index => $item)
                    <div class="recipe-item" style="display:flex; gap:1rem; align-items:flex-end; margin-bottom: 1rem; background: var(--surface-hover); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--border);">
                        <div style="flex: 2">
                            <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Master Resep</label>
                            <select name="master_recipes[{{ $index }}][id]" required style="width:100%">
                                <option value="">— Pilih Master Resep —</option>
                                @foreach($masterRecipes as $mr)
                                    <option value="{{ $mr->id }}" {{ $item->id == $mr->id ? 'selected' : '' }}>
                                        {{ $mr->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1">
                            <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Jumlah Pengali (x)</label>
                            <div style="display:flex; align-items:center; gap:0.5rem">
                                <input type="number" name="master_recipes[{{ $index }}][multiplier]" value="{{ number_format($item->pivot->multiplier, 4, '.', '') }}" step="any" min="0.0001" required style="width:100%">
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom: 2px" title="Hapus Master Resep">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @empty
                    <div id="empty-state" style="text-align:center; padding: 2rem; color: var(--muted); background: var(--surface-hover); border-radius: var(--radius); margin-bottom: 1rem;">
                        Belum ada master resep untuk menu ini.
                    </div>
                @endforelse
            </div>

            <button type="button" id="btn-add-ingredient" class="btn btn-ghost" style="width:100%; border: 1px dashed var(--border); margin-bottom: 1.5rem;">
                <i class="bi bi-plus-lg"></i> Tambah Master Resep
            </button>

            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-save"></i> Simpan Resep
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = {{ count($menu->masterRecipes) > 0 ? count($menu->masterRecipes) : 0 }};
            const container = document.getElementById('recipe-items-container');
            const btnAdd = document.getElementById('btn-add-ingredient');
            const emptyState = document.getElementById('empty-state');

            const template = `
                <div class="recipe-item" style="display:flex; gap:1rem; align-items:flex-end; margin-bottom: 1rem; background: var(--surface-hover); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="flex: 2">
                        <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Master Resep</label>
                        <select name="master_recipes[__INDEX__][id]" required style="width:100%">
                            <option value="">— Pilih Master Resep —</option>
                            @foreach($masterRecipes as $mr)
                                <option value="{{ $mr->id }}">
                                    {{ $mr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1">
                        <label style="font-size: 0.8rem; margin-bottom: 0.25rem; display:block;">Jumlah Pengali (x)</label>
                        <div style="display:flex; align-items:center; gap:0.5rem">
                            <input type="number" name="master_recipes[__INDEX__][multiplier]" step="any" min="0.0001" placeholder="1" required style="width:100%">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom: 2px" title="Hapus Master Resep">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;

            btnAdd.addEventListener('click', function() {
                if (emptyState) emptyState.style.display = 'none';
                
                const html = template.replace(/__INDEX__/g, itemIndex++);
                container.insertAdjacentHTML('beforeend', html);
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.recipe-item').remove();
                    if (container.querySelectorAll('.recipe-item').length === 0 && emptyState) {
                        emptyState.style.display = 'block';
                    }
                }
            });
        });
    </script>
@endsection
