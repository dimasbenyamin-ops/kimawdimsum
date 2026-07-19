@extends('layouts.guest')

@section('title', 'Masuk – Kumaw X Atmosphr')

@section('content')
    <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:1.5rem;text-align:center">Masuk ke Akun</h2>

    <form method="POST" action="{{ route('login.store') }}" autocomplete="on">
        @csrf

        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                autocomplete="username"
                required
                class="{{ $errors->has('username') ? 'is-invalid' : '' }}"
            >
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div style="position: relative;">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="current-password"
                    required
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    style="padding-right: 2.5rem;"
                >
                <button type="button" id="toggle-password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; font-size: 1.1rem; filter: grayscale(100%); opacity: 0.7; transition: all 0.2s;" title="Tampilkan Password">
                    👁️
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="checkbox-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" id="btn-login" class="btn-primary">Masuk</button>
        <div style="margin-top: 1rem; text-align: center;">
            <a href="{{ route('menu.index') }}" style="color: var(--muted); text-decoration: none; font-size: 0.875rem;">
                <i class="bi bi-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'password') {
                        toggleBtn.textContent = '👁️';
                        toggleBtn.title = 'Tampilkan Password';
                        toggleBtn.style.filter = 'grayscale(100%)';
                        toggleBtn.style.opacity = '0.7';
                    } else {
                        toggleBtn.textContent = '🙈';
                        toggleBtn.title = 'Sembunyikan Password';
                        toggleBtn.style.filter = 'none';
                        toggleBtn.style.opacity = '1';
                    }
                });
            }
        });
    </script>
@endsection
