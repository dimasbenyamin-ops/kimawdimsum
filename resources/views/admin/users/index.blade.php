@extends('layouts.admin')

@section('title', 'Manajemen User')

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
    
    .input-group-text.modern:hover {
        color: var(--text);
    }
    
    .input-group > .modern-form-control {
        border-radius: 8px 0 0 8px;
    }
    

    /* Refined Badges */
    .role-badge {
        background-color: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(96, 165, 250, 0.25);
        padding: 0.25rem 0.6rem;
        border-radius: 50rem;
        font-weight: 600;
        font-size: 0.75rem;
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
    
    /* Bootstrap 5 Utility Fallbacks (Ensures Grid works even if Bootstrap isn't globally active) */
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
    .flex-grow-1 { flex-grow: 1 !important; }
    .gap-2 { gap: 0.5rem !important; }
    .mb-4 { margin-bottom: 1.5rem !important; }
    .mb-3 { margin-bottom: 1rem !important; }
    .mb-0 { margin-bottom: 0 !important; }
    .mt-0 { margin-top: 0 !important; }
    .me-2 { margin-right: 0.5rem !important; }
    .ms-2 { margin-left: 0.5rem !important; }
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
    .form-floating > .form-control, .form-floating > .form-select { height: calc(3.5rem + 2px); padding: 1rem 0.75rem; }
    .form-floating > label { position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 1rem 0.75rem; pointer-events: none; border: 1px solid transparent; transform-origin: 0 0; transition: opacity .1s ease-in-out,transform .1s ease-in-out; color: var(--muted); }
    
    /* Hide placeholder when not focused to avoid overlapping with floating label */
    .form-floating > .form-control::placeholder { color: transparent; }
    
    /* Shift input text down and shrink label up when focused or has value */
    .form-floating > .form-control:focus, 
    .form-floating > .form-control:not(:placeholder-shown),
    .form-floating > .form-select { 
        padding-top: 1.625rem; 
        padding-bottom: 0.625rem; 
    }
    
    .form-floating > .form-control:focus ~ label, 
    .form-floating > .form-control:not(:placeholder-shown) ~ label, 
    .form-floating > .form-select ~ label { 
        opacity: .9; 
        transform: scale(.85) translateY(-0.75rem) translateX(0.15rem); 
        color: var(--gold);
        background: transparent;
    }
    
    .input-group { position: relative; display: flex; flex-wrap: wrap; align-items: stretch; width: 100%; }
    .input-group > .form-floating { flex: 1 1 auto; width: 1%; min-width: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 py-2">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.75rem; color: var(--text);">Manajemen User</h1>
            <p class="text-muted mt-0 mb-0" style="font-size: 0.9rem;">Kelola hak akses dan akun staf Kumaw X Atmosphr</p>
        </div>
    </div>


    <div class="row">
        
        {{-- LEFT COLUMN: User List Table (col-lg-8 / col-md-7) --}}
        <div class="col-lg-8 col-md-7">
            <div class="table-container shadow-sm">
                <div class="p-4" style="border-bottom: 1px solid var(--border);">
                    <h5 class="mb-0 fw-bold" style="font-size: 1.15rem; color: var(--text);">Daftar Pengguna</h5>
                </div>
                <div class="table-wrapper">
                    <table class="table-modern table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Username</th>
                                <th width="28%">Detail Profil</th>
                                <th width="15%">Role</th>
                                <th width="22%">Akses Valid</th>
                                <th width="10%" class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <span style="font-family: monospace; font-size: 0.95rem; font-weight: 600; color: var(--gold);">{{ e($user->username) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--text); font-size: 0.95rem;">{{ e($user->name) }}</div>
                                        <div style="font-size: 0.8rem; color: var(--muted); margin-top: 0.15rem;">{{ e($user->email) }}</div>
                                    </td>
                                    <td>
                                        <span class="role-badge">
                                            {{ e($user->role?->name ?? '—') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->expired_at)
                                            <div style="font-size: 0.85rem;" class="{{ $user->isExpired() ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $user->expired_at->format('d/m/Y') }}
                                                @if($user->isExpired())
                                                    <span class="d-block" style="font-size: 0.7rem; color: var(--error); margin-top: 0.1rem;">(Kadaluarsa)</span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="color: var(--muted); font-size: 0.85rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle actions-cell">
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit" title="Edit User">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user {{ e($user->name) }}?')" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Hapus User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-5" style="color: var(--muted); font-style: italic;">
                                        <div class="py-4">
                                            <i class="bi bi-people fs-1 mb-3 d-block" style="opacity: 0.3; font-size: 3rem;"></i>
                                            Belum ada user terdaftar di sistem.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="p-3" style="border-top: 1px solid var(--border);">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: Add User Form Card (col-lg-4 / col-md-5) --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 rounded-4 shadow-sm sticky-form-card" style="background: var(--surface);">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="color: var(--text); border-bottom: 2px solid var(--gold); display: inline-flex; padding-bottom: 0.6rem; font-size: 1.15rem;">
                        <i class="bi bi-person-plus-fill me-2" style="color: var(--gold);"></i> Tambah User Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        {{-- Name --}}
                        <div class="form-floating mb-3">
                            <input type="text" name="name" class="form-control modern-form-control @error('name') is-invalid @enderror" id="nameInput" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                            <label for="nameInput">Nama Lengkap</label>
                        </div>

                        {{-- Username --}}
                        <div class="form-floating mb-3">
                            <input type="text" name="username" class="form-control modern-form-control @error('username') is-invalid @enderror" id="usernameInput" placeholder="Username" value="{{ old('username') }}" required>
                            <label for="usernameInput">Username</label>
                        </div>
                        
                        {{-- Email --}}
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control modern-form-control @error('email') is-invalid @enderror" id="emailInput" placeholder="Email" value="{{ old('email') }}" required>
                            <label for="emailInput">Alamat Email</label>
                        </div>
                        
                        {{-- Phone (Optional) --}}
                        <div class="form-floating mb-3">
                            <input type="tel" name="phone" class="form-control modern-form-control @error('phone') is-invalid @enderror" id="phoneInput" placeholder="Nomor Telepon" value="{{ old('phone') }}">
                            <label for="phoneInput">No. Telepon (Opsional)</label>
                        </div>

                        {{-- Role --}}
                        <div class="form-floating mb-3">
                            <select name="role_id" class="form-select modern-form-control @error('role_id') is-invalid @enderror" id="roleSelect" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <label for="roleSelect">Hak Akses (Role)</label>
                        </div>

                        {{-- Password with Show/Hide Toggle --}}
                        <div class="input-group mb-3">
                            <div class="form-floating flex-grow-1">
                                <input type="password" name="password" class="form-control modern-form-control border-end-0 @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password" required>
                                <label for="passwordInput">Password</label>
                            </div>
                            <span class="input-group-text modern border-start-0" id="togglePasswordBtn" onclick="togglePassword()" title="Tampilkan Password">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </span>
                        </div>

                        {{-- Expired At --}}
                        <div class="form-floating mb-4">
                            <input type="date" name="expired_at" class="form-control modern-form-control @error('expired_at') is-invalid @enderror" id="expiredInput" value="{{ old('expired_at') }}">
                            <label for="expiredInput">Tanggal Exp User (Opsional)</label>
                            <div class="mt-2" style="font-size: 0.75rem; color: var(--muted);"><i class="bi bi-info-circle me-1"></i> Kosongkan jika user aktif selamanya</div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-gold w-100 py-3 fw-bold rounded-4 shadow-sm d-flex justify-content-center align-items-center gap-2" style="font-size: 1rem;">
                            <i class="bi bi-person-check-fill fs-5"></i> Simpan User Baru
                        </button>
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
