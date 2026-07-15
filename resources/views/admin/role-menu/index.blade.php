@extends('layouts.admin')

@section('title', 'Role Menu: ' . e($role->name))

@section('styles')
<style>
    .modern-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    
    .back-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--surface-hover);
        color: var(--text);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .back-btn:hover {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        transform: translateX(-3px);
    }
    
    
    .btn-outline-modern {
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text);
        transition: all 0.2s ease;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-outline-modern:hover {
        background: var(--surface-hover);
        border-color: #94a3b8;
    }
    
    /* Custom switch styling */
    .menu-item-card {
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
        background: rgba(255,255,255,0.01);
        margin-bottom: 1rem;
        height: 100%;
    }
    
    .menu-item-card:hover {
        border-color: rgba(37, 99, 235, 0.3);
        background: rgba(37, 99, 235, 0.02);
    }
    
    .custom-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-top: 0;
        cursor: pointer;
    }
    
    .custom-switch .form-check-input:checked {
        background-color: #10b981; /* green */
        border-color: #10b981;
    }
    
    .custom-switch .form-check-label {
        font-weight: 600;
        cursor: pointer;
        padding-left: 0.75rem;
        font-size: 1.05rem;
        color: var(--text);
    }
    
    .child-menu-box {
        margin-top: 1rem;
        padding-left: 3rem;
        border-left: 2px dashed var(--border);
        margin-left: 1.5rem;
    }
    
    .child-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        cursor: pointer;
    }
    
    .child-switch .form-check-input:checked {
        background-color: #3b82f6; /* blue */
        border-color: #3b82f6;
    }
    
    .child-switch .form-check-label {
        font-weight: 500;
        cursor: pointer;
        padding-left: 0.5rem;
        color: var(--muted);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 px-0 max-w-4xl mx-auto" style="max-width: 1000px;">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('admin.roles.index') }}" class="back-btn shadow-sm">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--text);">Hak Akses Menu</h2>
            <p class="mb-0 text-muted">Role: <span class="badge bg-primary px-3 py-2 ms-1 rounded-pill">{{ e($role->name) }}</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid var(--success);">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="modern-card p-4 p-md-5">
        <div class="alert mb-4" style="background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.1); border-radius: 0.75rem; color: var(--text);">
            <i class="bi bi-info-circle-fill text-primary me-2"></i>
            Atur menu apa saja yang dapat diakses oleh staf dengan role <strong class="text-primary">{{ e($role->name) }}</strong>. Aktifkan *toggle* untuk memberi izin akses. Perubahan langsung aktif setelah disimpan.
        </div>

        <form method="POST" action="{{ route('admin.role-menu.sync', $role) }}" id="form-role-menu">
            @csrf

            <div class="row g-3">
                @foreach($navMenus as $menu)
                    <div class="col-12 col-md-6">
                        <div class="menu-item-card">
                            {{-- Top-level menu --}}
                            <div class="form-check form-switch custom-switch d-flex align-items-center">
                                <input class="form-check-input flex-shrink-0 shadow-sm" type="checkbox" role="switch"
                                       id="nav_menu_{{ $menu->id }}"
                                       name="nav_menu_ids[]"
                                       value="{{ $menu->id }}"
                                       {{ in_array($menu->id, $assignedIds) ? 'checked' : '' }}>
                                <label class="form-check-label w-100 d-flex align-items-center" for="nav_menu_{{ $menu->id }}">
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }} fs-5 me-2 text-primary" style="opacity: 0.8;"></i>
                                    @endif
                                    {{ e($menu->name) }}
                                </label>
                            </div>

                            {{-- Sub-menus --}}
                            @if($menu->children->isNotEmpty())
                                <div class="child-menu-box">
                                    @foreach($menu->children as $child)
                                        <div class="form-check form-switch child-switch d-flex align-items-center mb-3">
                                            <input class="form-check-input flex-shrink-0 shadow-sm" type="checkbox" role="switch"
                                                   id="nav_menu_{{ $child->id }}"
                                                   name="nav_menu_ids[]"
                                                   value="{{ $child->id }}"
                                                   {{ in_array($child->id, $assignedIds) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 d-flex align-items-center" for="nav_menu_{{ $child->id }}">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} fs-6 me-2" style="opacity: 0.6;"></i>
                                                @endif
                                                {{ e($child->name) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <hr class="my-4" style="border-color: var(--border);">

            <div class="d-flex gap-3 justify-content-end align-items-center mt-2">
                <a href="{{ route('admin.roles.index') }}" class="btn-outline-modern">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" id="btn-simpan-rolemenu" class="btn btn-gold">
                    <i class="bi bi-shield-check"></i> Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
