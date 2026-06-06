@extends('layouts.guest')

@section('title', 'Masuk – Kumaw Dimsum')

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
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Minimal 8 karakter"
                autocomplete="current-password"
                required
                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="checkbox-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" id="btn-login" class="btn-primary">Masuk</button>
    </form>
@endsection
