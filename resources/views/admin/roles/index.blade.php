@extends('layouts.admin')

@section('title', 'Manajemen Role')

@section('styles')
<style>
    /* Modern Overrides & sticky positioning */
    .sticky-form-card {
        position: sticky;
        top: calc(var(--topbar-h) + 1.5rem);
        z-index: 10;
    }
    
    .table-hover tbody tr:hover {
        background-color: var(--surface-hover) !important;
    }
    
    .modern-form-control {
        border-radius: 8px;
        background-color: var(--surface) !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        box-shadow: none !important;
        appearance: none;
    }
    
    .modern-form-control:focus {
        border-color: var(--gold) !important;
        background-color: var(--surface) !important;
        color: var(--text) !important;
        box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.15) !important;
    }
    
    .form-floating > label {
        color: var(--muted);
    }
    
    .form-floating > .modern-form-control:focus ~ label,
    .form-floating > .modern-form-control:not(:placeholder-shown) ~ label {
        color: var(--gold);
        background: transparent;
    }
    
    .btn-gradient-primary {
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: white;
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
        color: white;
    }
    
    .table-container {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background-color: var(--surface);
    }
    
    .table-modern {
        margin-bottom: 0;
        color: var(--text);
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    .table-modern thead th {
        background-color: rgba(255,255,255,0.03);
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        text-align: left;
    }
    
    .table-modern tbody td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        vertical-align: middle;
    }
    
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.05);
        color: #94a3b8 !important;
        text-decoration: none;
        font-size: 1rem;
        cursor: pointer;
    }
    
    .action-btn.edit:hover {
        background: rgba(96, 165, 250, 0.15);
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.4);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.15);
    }
    
    .action-btn.delete {
        cursor: pointer;
        background: transparent;
        border: none;
    }
    .action-btn.delete:hover {
        background: rgba(248, 113, 113, 0.15);
        color: #f87171 !important;
        border-color: rgba(248, 113, 113, 0.4);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(248, 113, 113, 0.15);
    }

    .actions-cell {
        white-space: nowrap;
    }

    /* Bootstrap 5 Utility Fallbacks */
    .container-fluid { width: 100%; padding-right: 1.5rem; padding-left: 1.5rem; margin-right: auto; margin-left: auto; }
    .row { display: flex; flex-wrap: wrap; margin-top: -1.5rem; margin-right: -0.75rem; margin-left: -0.75rem; }
    .row > * { box-sizing: border-box; flex-shrink: 0; width: 100%; max-width: 100%; padding-right: 0.75rem; padding-left: 0.75rem; margin-top: 1.5rem; }
    @media (min-width: 992px) {
        .col-lg-8 { flex: 0 0 auto; width: 66.66666667%; }
        .col-lg-4 { flex: 0 0 auto; width: 33.33333333%; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .col-md-7 { flex: 0 0 auto; width: 58.33333333%; }
        .col-md-5 { flex: 0 0 auto; width: 41.66666667%; }
    }
    .d-flex { display: flex !important; }
    .align-items-center { align-items: center !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-center { justify-content: center !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 1rem !important; }
    .mb-4 { margin-bottom: 1.5rem !important; }
    .mb-3 { margin-bottom: 1rem !important; }
    .mb-0 { margin-bottom: 0 !important; }
    .mt-0 { margin-top: 0 !important; }
    .me-2 { margin-right: 0.5rem !important; }
    .p-4 { padding: 1.5rem !important; }
    .py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .w-100 { width: 100% !important; }
    .fw-bold { font-weight: 700 !important; }
    .text-center { text-align: center !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
    .rounded-4 { border-radius: 1rem !important; }
    .border-0 { border: 0 !important; }
    
    /* Form floating Fallback */
    .form-floating { position: relative; }
    .form-floating > .form-control { height: calc(3.5rem + 2px); padding: 1rem 0.75rem; }
    .form-floating > label { position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 1rem 0.75rem; pointer-events: none; border: 1px solid transparent; transform-origin: 0 0; transition: opacity .1s ease-in-out,transform .1s ease-in-out; color: var(--muted); }
    
    /* Hide placeholder when not focused to avoid overlapping with floating label */
    .form-floating > .form-control::placeholder { color: transparent; }
    
    /* Shift input text down and shrink label up when focused or has value */
    .form-floating > .form-control:focus, 
    .form-floating > .form-control:not(:placeholder-shown) { 
        padding-top: 1.625rem; 
        padding-bottom: 0.625rem; 
    }
    
    .form-floating > .form-control:focus ~ label, 
    .form-floating > .form-control:not(:placeholder-shown) ~ label { 
        opacity: .9; 
        transform: scale(.85) translateY(-0.75rem) translateX(0.15rem); 
        color: var(--gold);
        background: transparent;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 py-2">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.75rem; color: var(--text);">Manajemen Role</h1>
            <p class="text-muted mt-0 mb-0" style="font-size: 0.9rem;">Kelola klasifikasi hak akses staf Kumaw Dimsum</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm" role="alert" style="padding: 1rem 1.25rem; border-radius: 12px; border-left: 4px solid var(--success);">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start mb-4 shadow-sm" role="alert" style="padding: 1rem 1.25rem; border-radius: 12px; border-left: 4px solid var(--error); background: rgba(248,113,113,0.1); color: var(--error);">
            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
            <div>
                <ul class="mb-0 ps-3" style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="row">
        
        {{-- LEFT COLUMN: Roles List Table (col-lg-8 / col-md-7) --}}
        <div class="col-lg-8 col-md-7">
            <div class="table-container shadow-sm">
                <div class="p-4" style="border-bottom: 1px solid var(--border);">
                    <h5 class="mb-0 fw-bold" style="font-size: 1.15rem; color: var(--text);">Daftar Role Sistem</h5>
                </div>
                <div class="table-wrapper">
                    <table class="table-modern table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th width="45%">Nama Role</th>
                                <th width="30%">Jumlah Pengguna</th>
                                <th width="15%" class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--text); font-size: 0.95rem;">{{ e($role->name) }}</div>
                                    </td>
                                    <td>
                                        <span style="color: var(--muted); font-size: 0.9rem;">
                                            <i class="bi bi-people me-1"></i> {{ $role->users_count }} user
                                        </span>
                                    </td>
                                    <td class="text-center align-middle actions-cell">
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="action-btn edit" title="Edit Role">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Hapus role {{ e($role->name) }}? Semua user dengan role ini akan kehilangan akses.')" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Hapus Role">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-5" style="color: var(--muted); font-style: italic;">
                                        <div class="py-4">
                                            <i class="bi bi-shield-lock fs-1 mb-3 d-block" style="opacity: 0.3; font-size: 3rem;"></i>
                                            Belum ada role terdaftar di sistem.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Add Role Form Card (col-lg-4 / col-md-5) --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 rounded-4 shadow-sm sticky-form-card" style="background: var(--surface);">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="color: var(--text); border-bottom: 2px solid var(--gold); display: inline-flex; padding-bottom: 0.6rem; font-size: 1.15rem;">
                        <i class="bi bi-shield-plus me-2" style="color: var(--gold);"></i> Tambah Role Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        {{-- Name --}}
                        <div class="form-floating mb-4">
                            <input type="text" name="name" class="form-control modern-form-control @error('name') is-invalid @enderror" id="nameInput" placeholder="Nama Role (Misal: Supervisor)" value="{{ old('name') }}" required>
                            <label for="nameInput">Nama Role</label>
                            <div class="mt-2" style="font-size: 0.75rem; color: var(--muted);"><i class="bi bi-info-circle me-1"></i> Digunakan untuk klasifikasi akses staf.</div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-gradient-primary w-100 py-3 fw-bold rounded-4 shadow-sm d-flex justify-content-center align-items-center gap-2" style="font-size: 1rem;">
                            <i class="bi bi-save2 fs-5"></i> Simpan Role Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
