@extends('layouts.admin')

@section('title', 'Tambah Role')

@section('content')
<div class="py-6 max-w-md">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="bi bi-arrow-left-circle text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Role</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                    Nama Role <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. Administrator, Kasir"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                              {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" id="btn-simpan-role"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('admin.roles.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-6 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
