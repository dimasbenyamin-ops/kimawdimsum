@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
<div class="py-6 max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="bi bi-arrow-left-circle text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Tambah User</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
            @csrf

            {{-- Username --}}
            <div class="mb-4">
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="username" name="username"
                       value="{{ old('username') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('username') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required autocomplete="username">
                @error('username')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required autocomplete="email">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password" name="password"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required autocomplete="new-password"
                       minlength="8" maxlength="128">
                <p class="mt-1 text-xs text-gray-400">Minimal 8 karakter.</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div class="mb-4">
                <label for="role_id" class="block text-sm font-semibold text-gray-700 mb-1">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role_id" name="role_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                               {{ $errors->has('role_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                        required>
                    <option value="">— Pilih Role —</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ e($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Exp User --}}
            <div class="mb-6">
                <label for="expired_at" class="block text-sm font-semibold text-gray-700 mb-1">
                    Tanggal Exp User
                    <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="date" id="expired_at" name="expired_at"
                       value="{{ old('expired_at') }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('expired_at') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ada batas waktu.</p>
                @error('expired_at')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        id="btn-simpan-user"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-6 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
