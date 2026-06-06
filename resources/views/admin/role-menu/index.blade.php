@extends('layouts.admin')

@section('title', 'Role Menu: ' . e($role->name))

@section('content')
<div class="py-6 max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="bi bi-arrow-left-circle text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hak Akses Menu</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Role: <span class="font-semibold text-blue-600">{{ e($role->name) }}</span>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-500 mb-5">
            Centang menu yang boleh diakses oleh role <strong>{{ e($role->name) }}</strong>.
            Perubahan langsung berlaku saat disimpan.
        </p>

        <form method="POST" action="{{ route('admin.role-menu.sync', $role) }}" id="form-role-menu">
            @csrf

            <div class="space-y-3">
                @foreach($navMenus as $menu)
                    {{-- Top-level menu item --}}
                    <div class="border border-gray-100 rounded-lg p-3">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox"
                                   id="nav_menu_{{ $menu->id }}"
                                   name="nav_menu_ids[]"
                                   value="{{ $menu->id }}"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-400"
                                   {{ in_array($menu->id, $assignedIds) ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-700">
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} mr-1"></i>
                                @endif
                                {{ e($menu->name) }}
                            </span>
                        </label>

                        {{-- Sub-menus (children) --}}
                        @if($menu->children->isNotEmpty())
                            <div class="ml-7 mt-2 space-y-2">
                                @foreach($menu->children as $child)
                                    <label class="flex items-center gap-3 cursor-pointer select-none">
                                        <input type="checkbox"
                                               id="nav_menu_{{ $child->id }}"
                                               name="nav_menu_ids[]"
                                               value="{{ $child->id }}"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-400"
                                               {{ in_array($child->id, $assignedIds) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-600">
                                            @if($child->icon)
                                                <i class="{{ $child->icon }} mr-1"></i>
                                            @endif
                                            {{ e($child->name) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        id="btn-simpan-rolemenu"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                    Simpan Hak Akses
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
