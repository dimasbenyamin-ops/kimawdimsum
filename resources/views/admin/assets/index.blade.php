@extends('layouts.admin')

@section('title', 'Manajemen Aset & Inventaris – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>📦 Manajemen Aset & Inventaris</h1>
            <p>Catat dan kelola barang inventaris resto yang tidak habis pakai (piring, panci, mesin kasir, dll).</p>
        </div>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Catat Aset Baru
        </a>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--gold); display: flex; align-items: center; gap: 1.5rem;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--gold-dim); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-safe2"></i>
            </div>
            <div>
                <p style="color: var(--muted); margin: 0 0 0.25rem 0; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Total Nilai Aset (Kondisi Bagus)</p>
                <h3 style="margin: 0; font-size: 1.5rem; color: var(--text);">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.assets.index') }}" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Aset..." class="form-control" style="max-width:250px">
                
                <select name="condition" class="form-control" style="max-width:200px" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="good" {{ request('condition') === 'good' ? 'selected' : '' }}>Bagus (Good)</option>
                    <option value="damaged" {{ request('condition') === 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                    <option value="lost" {{ request('condition') === 'lost' ? 'selected' : '' }}>Hilang (Lost)</option>
                </select>

                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Filter</button>
                @if(request('search') || request('condition'))
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Beli</th>
                        <th>Nama Aset</th>
                        <th style="text-align:right">Kuantitas</th>
                        <th style="text-align:right">Harga Satuan (Rp)</th>
                        <th style="text-align:right">Total Nilai (Rp)</th>
                        <th style="text-align:center">Kondisi</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $index => $asset)
                        <tr>
                            <td>{{ $assets->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') }}</td>
                            <td style="font-weight:600">{{ $asset->name }}</td>
                            <td style="text-align:right">{{ number_format($asset->quantity, 0, ',', '.') }}</td>
                            <td style="text-align:right">{{ number_format($asset->price_per_item, 0, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:600; color:var(--success)">{{ number_format($asset->total_price, 0, ',', '.') }}</td>
                            <td style="text-align:center">
                                @if($asset->condition === 'good')
                                    <span class="badge" style="background:var(--success-dim); color:var(--success)">Bagus</span>
                                @elseif($asset->condition === 'damaged')
                                    <span class="badge" style="background:var(--warning-dim); color:#b45309">Rusak</span>
                                @else
                                    <span class="badge" style="background:var(--danger-dim); color:var(--danger)">Hilang</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex; gap:0.5rem; justify-content:center">
                                    <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-ghost" style="padding:0.5rem; color:var(--gold)" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data aset ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost" style="padding:0.5rem; color:var(--danger)" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:3rem; color:var(--muted)">
                                Belum ada data aset yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
@endsection
