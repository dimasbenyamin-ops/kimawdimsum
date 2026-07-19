@extends('layouts.admin')

@section('title', 'Edit User: ' . e($user->name))

@section('styles')
<style>
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
    
    /* Form floating Fallback */
    .form-floating { position: relative; }
    .form-floating > .form-control, .form-floating > .form-select { height: calc(3.5rem + 2px); padding: 1rem 0.75rem; }
    .form-floating > label { position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 1rem 0.75rem; pointer-events: none; border: 1px solid transparent; transform-origin: 0 0; transition: opacity .1s ease-in-out,transform .1s ease-in-out; color: var(--muted); }
    .form-floating > .form-control::placeholder { color: transparent; }
    .form-floating > .form-control:focus, 
    .form-floating > .form-control:not(:placeholder-shown),
    .form-floating > .form-select { padding-top: 1.625rem; padding-bottom: 0.625rem; }
    .form-floating > .form-control:focus ~ label, 
    .form-floating > .form-control:not(:placeholder-shown) ~ label, 
    .form-floating > .form-select ~ label { opacity: .9; transform: scale(.85) translateY(-0.75rem) translateX(0.15rem); color: var(--gold); background: transparent; }

    /* Input group tweaks for the dark theme */
    .input-group-text.modern {
        background-color: var(--surface);
        border: 1px solid var(--border);
        color: var(--muted);
        cursor: pointer;
        border-radius: 0 8px 8px 0;
        display: flex;
        align-items: center;
        padding: 0 1rem;
    }
    .input-group-text.modern:hover { color: var(--text); }
    .input-group > .modern-form-control { border-radius: 8px 0 0 8px; }
    .input-group { position: relative; display: flex; flex-wrap: wrap; align-items: stretch; width: 100%; }
    .input-group > .form-floating { flex: 1 1 auto; width: 1%; min-width: 0; }
    
    /* Bootstrap 5 Utility Fallbacks */
    .container-fluid { width: 100%; padding-right: 1.5rem; padding-left: 1.5rem; margin-right: auto; margin-left: auto; }
    .row { display: flex; flex-wrap: wrap; margin-top: -1.5rem; margin-right: -0.75rem; margin-left: -0.75rem; }
    .row > * { box-sizing: border-box; flex-shrink: 0; width: 100%; max-width: 100%; padding-right: 0.75rem; padding-left: 0.75rem; margin-top: 1.5rem; }
    @media (min-width: 992px) { .col-lg-8 { flex: 0 0 auto; width: 66.66666667%; } }
    @media (min-width: 768px) { .col-md-6 { flex: 0 0 auto; width: 50%; } }
    .d-flex { display: flex !important; }
    .align-items-center { align-items: center !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-center { justify-content: center !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 1rem !important; }
    .mb-4 { margin-bottom: 1.5rem !important; }
    .mb-3 { margin-bottom: 1rem !important; }
    .mb-0 { margin-bottom: 0 !important; }
    .mt-0 { margin-top: 0 !important; }
    .mt-n2 { margin-top: -0.5rem !important; }
    .me-1 { margin-right: 0.25rem !important; }
    .me-2 { margin-right: 0.5rem !important; }
    .p-4 { padding: 1.5rem !important; }
    .px-4 { padding-right: 1.5rem !important; padding-left: 1.5rem !important; }
    .px-5 { padding-right: 3rem !important; padding-left: 3rem !important; }
    .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    .w-100 { width: 100% !important; }
    .fw-bold { font-weight: 700 !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
    .rounded-4 { border-radius: 1rem !important; }
    .border-0 { border: 0 !important; }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 py-2">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.75rem; color: var(--text);">Edit User</h1>
            <p class="text-muted mt-0 mb-0" style="font-size: 0.9rem;">Perbarui data staf Kumaw X Atmosphr</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-4 px-4 shadow-sm" style="color: var(--text); border-color: var(--border);">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm" style="background: var(--surface);">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="color: var(--text); border-bottom: 2px solid var(--gold); display: inline-flex; padding-bottom: 0.6rem; font-size: 1.15rem;">
                        <i class="bi bi-person-lines-fill me-2" style="color: var(--gold);"></i> Form Edit Data User
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                {{-- Name --}}
                                <div class="form-floating mb-4">
                                    <input type="text" name="name" class="form-control modern-form-control @error('name') is-invalid @enderror" id="nameInput" placeholder="Nama Lengkap" value="{{ old('name', $user->name) }}" required>
                                    <label for="nameInput">Nama Lengkap <span class="text-danger">*</span></label>
                                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Username --}}
                                <div class="form-floating mb-4">
                                    <input type="text" name="username" class="form-control modern-form-control @error('username') is-invalid @enderror" id="usernameInput" placeholder="Username" value="{{ old('username', $user->username) }}" required autocomplete="username">
                                    <label for="usernameInput">Username <span class="text-danger">*</span></label>
                                    @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{-- Email --}}
                                <div class="form-floating mb-4">
                                    <input type="email" name="email" class="form-control modern-form-control @error('email') is-invalid @enderror" id="emailInput" placeholder="Email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                    <label for="emailInput">Alamat Email <span class="text-danger">*</span></label>
                                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Role --}}
                                <div class="form-floating mb-4">
                                    <select name="role_id" class="form-select modern-form-control @error('role_id') is-invalid @enderror" id="roleSelect" required>
                                        <option value="" disabled>-- Pilih Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="roleSelect">Hak Akses (Role) <span class="text-danger">*</span></label>
                                    @error('role_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{-- Expired At --}}
                                <div class="form-floating mb-4">
                                    <input type="date" name="expired_at" class="form-control modern-form-control @error('expired_at') is-invalid @enderror" id="expiredInput" value="{{ old('expired_at', $user->expired_at?->format('Y-m-d')) }}">
                                    <label for="expiredInput">Tanggal Exp User (Opsional)</label>
                                    <div class="mt-2" style="font-size: 0.75rem; color: var(--muted);"><i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak ada batas waktu</div>
                                    @error('expired_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Password with Show/Hide Toggle --}}
                                <div class="input-group mb-4">
                                    <div class="form-floating flex-grow-1">
                                        <input type="password" name="password" class="form-control modern-form-control border-end-0 @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password Baru" autocomplete="new-password">
                                        <label for="passwordInput">Password Baru (Opsional)</label>
                                    </div>
                                    <span class="input-group-text modern border-start-0" id="togglePasswordBtn" onclick="togglePassword()" title="Tampilkan Password">
                                        <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                <div class="mt-n2" style="font-size: 0.75rem; color: var(--muted);"><i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak ingin mengubah password (min: 8 karakter)</div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.1); margin-top: 2rem; margin-bottom: 1.5rem;">

                        {{-- Submit Button --}}
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-4 px-4 py-2 shadow-sm d-flex align-items-center" style="color: var(--text); border-color: var(--border);">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-gold px-5 py-2 fw-bold rounded-4 shadow-sm d-flex align-items-center gap-2">
                                <i class="bi bi-save2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('passwordInput');
        const icon = document.getElementById('togglePasswordIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>
@endsection
