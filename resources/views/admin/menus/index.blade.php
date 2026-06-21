@extends('layouts.admin')

@section('title', 'Kelola Menu – Kumaw Dimsum Admin')

@section('styles')
<style>
    .menu-table-wrap { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    thead th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    tbody td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr { transition: background 0.15s; }
    tbody tr:hover { background: rgba(255,255,255,0.02); }

    .avail-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.625rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .avail-yes { background: rgba(52,211,153,0.12); color: var(--success); border: 1px solid rgba(52,211,153,0.3); }
    .avail-no  { background: rgba(248,113,113,0.1); color: var(--error);   border: 1px solid rgba(248,113,113,0.2); }

    .action-btns { display: flex; gap: 0.5rem; align-items: center; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🍽️ Kelola Menu</h1>
            <p>Tambah, ubah, dan hapus item menu dimsum</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-gold" id="btn-add-menu">
            ➕ Tambah Menu
        </a>
    </div>

    <div class="card">
        @if($menus->isEmpty())
            <div class="empty-state">
                <div class="icon">🥟</div>
                <h3>Belum ada menu</h3>
                <p>Mulai tambahkan item menu pertama kamu.</p>
                <a href="{{ route('admin.menus.create') }}" class="btn btn-gold">Tambah Menu</a>
            </div>
        @else
            <div class="menu-table-wrap">
                <div class="table-wrapper">
<table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                            <tr>
                                <td style="color:var(--muted)">{{ $menu->id }}</td>
                                <td>
                                    <div style="font-weight:600">{{ $menu->name }}</div>
                                    @if($menu->description)
                                        <div style="font-size:0.8rem;color:var(--muted);margin-top:0.125rem">
                                            {{ Str::limit($menu->description, 60) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:0.8125rem;color:var(--muted)">
                                        {{ $categoryLabels[$menu->category] ?? ucfirst($menu->category) }}
                                    </span>
                                </td>
                                <td style="font-weight:700;color:var(--gold)">{{ $menu->formattedPrice }}</td>
                                <td style="color:var(--muted)">{{ $menu->sort_order }}</td>
                                <td>
                                    <span class="avail-badge {{ $menu->is_available ? 'avail-yes' : 'avail-no' }}">
                                        {{ $menu->is_available ? '● Tersedia' : '○ Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('admin.menus.edit', $menu) }}"
                                           class="btn btn-ghost btn-sm"
                                           id="edit-menu-{{ $menu->id }}">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                              onsubmit="return confirm('Hapus menu {{ addslashes($menu->name) }}? Pesanan lama tidak terpengaruh.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    id="delete-menu-{{ $menu->id }}">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
</div>
            </div>
        @endif
    </div>
@endsection
