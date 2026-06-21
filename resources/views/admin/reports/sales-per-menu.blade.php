@extends('layouts.admin')

@section('title', 'Laporan Penjualan per Menu – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>📊 Penjualan per Menu</h1>
            <p>Menu mana yang paling laku dan paling menguntungkan.</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <div style="padding:1.5rem;">
            <form method="GET" action="{{ route('admin.reports.sales-per-menu') }}" style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
                <div>
                    <label style="font-size:0.875rem; color:var(--muted); display:block; margin-bottom:0.25rem">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div>
                    <label style="font-size:0.875rem; color:var(--muted); display:block; margin-bottom:0.25rem">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <button type="submit" class="btn btn-gold" style="padding:0.5rem 1rem">Terapkan Filter</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">Rank</th>
                        <th>Menu</th>
                        <th style="text-align:center">Kategori</th>
                        <th style="text-align:right">Terjual (Porsi)</th>
                        <th style="text-align:right">Total Revenue (Rp)</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $item)
                        <tr>
                            <td style="color:var(--muted); text-align:center">
                                @if($index === 0) 🥇
                                @elseif($index === 1) 🥈
                                @elseif($index === 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500">{{ $item->menu->name ?? 'Menu Terhapus' }}</div>
                            </td>
                            <td style="text-align:center">
                                <span class="badge" style="background:var(--surface-hover)">{{ ucfirst($item->menu->category ?? '-') }}</span>
                            </td>
                            <td style="text-align:right; font-weight:bold; font-size:1.1rem">
                                {{ $item->total_qty }}
                            </td>
                            <td style="text-align:right; font-family:monospace; color:var(--success)">
                                {{ number_format($item->total_revenue, 2, ',', '.') }}
                            </td>
                            <td style="text-align:center">
                                <form action="{{ route('admin.reports.sales-per-menu.destroy', $item->menu_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA data penjualan untuk menu ini pada rentang tanggal tersebut? (Tindakan ini tidak bisa dibatalkan)');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                                    <button type="submit" class="btn btn-sm btn-ghost" title="Hapus Data Penjualan">
                                        <i class="bi bi-trash3" style="color:var(--danger)"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 3rem; color:var(--muted)">
                                Belum ada data penjualan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
