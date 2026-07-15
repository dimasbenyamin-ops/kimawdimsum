@extends('layouts.admin')

@section('title', 'Manajemen Menu Sidebar – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>🗂️ Manajemen Menu Sidebar</h1>
            <p>Kelola menu yang tampil di sidebar kiri (nama, rute, ikon, urutan).</p>
        </div>
        <a href="{{ route('admin.nav-menus.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Tambah Menu Baru
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.nav-menus.index') }}" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Menu atau Route..." class="form-control" style="max-width:300px">
                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.nav-menus.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nama Menu</th>
                        <th>Route Name</th>
                        <th>Ikon</th>
                        <th>Induk Menu (Parent)</th>
                        <th style="text-align:center">Sort Order</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($navMenus as $index => $menu)
                        <tr>
                            <td style="color:var(--muted)">{{ $navMenus->firstItem() + $index }}</td>
                            <td style="font-weight: {{ $menu->parent_id ? 'normal' : 'bold' }}">
                                @if($menu->parent_id)
                                    <span style="color:var(--muted); margin-right:5px">↳</span>
                                @endif
                                {{ $menu->name }}
                            </td>
                            <td><code style="background:var(--surface-hover); padding:0.2rem 0.5rem; border-radius:4px; font-size:0.875rem">{{ $menu->route_name }}</code></td>
                            <td>
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }}"></i> ({{ $menu->icon }})
                                @else
                                    <span style="color:var(--muted)">-</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->parent)
                                    {{ $menu->parent->name }}
                                @else
                                    <span style="color:var(--muted); font-style:italic">Tidak ada (Menu Utama)</span>
                                @endif
                            </td>
                            <td style="text-align:center">{{ $menu->sort_order }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.nav-menus.edit', $menu) }}" class="btn btn-sm btn-ghost" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.nav-menus.destroy', $menu) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost" title="Hapus">
                                        <i class="bi bi-trash3" style="color:var(--danger)"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 3rem; color:var(--muted)">
                                Belum ada menu sidebar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($navMenus->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $navMenus->links() }}
            </div>
        @endif
    </div>
@endsection
