@extends('layouts.admin')

@section('title', 'Stock Opname – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>📦 Stock Opname</h1>
            <p>Lakukan audit fisik stok bahan baku dan sesuaikan dengan sistem.</p>
        </div>
        <a href="{{ route('admin.stock-opnames.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Buat Stock Opname
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.stock-opnames.index') }}" style="display:flex; gap:1rem; align-items:center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. SO..." class="form-control" style="max-width:250px">
                
                <select name="status" class="form-control" style="max-width:150px" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                </select>

                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Cari</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.stock-opnames.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nomor SO</th>
                        <th>Tanggal</th>
                        <th>Pencatat</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $index => $opname)
                        <tr>
                            <td style="color:var(--muted)">{{ $opnames->firstItem() + $index }}</td>
                            <td>
                                <div style="font-weight: 500; color:var(--text)">{{ $opname->opname_number }}</div>
                            </td>
                            <td>{{ $opname->opname_date->format('d M Y') }}</td>
                            <td>{{ $opname->creator->name ?? 'System' }}</td>
                            <td>
                                @if($opname->status === 'draft')
                                    <span class="badge" style="background:#fef3c7; color:#92400e">Draft</span>
                                @elseif($opname->status === 'confirmed')
                                    <span class="badge" style="background:#d1fae5; color:#065f46">Confirmed</span>
                                @else
                                    <span class="badge" style="background:#fee2e2; color:#991b1b">Cancelled</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.stock-opnames.show', $opname) }}" class="btn btn-sm btn-ghost" title="Detail SO">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 3rem; color:var(--muted)">
                                @if($search || $status)
                                    Tidak ada data stock opname yang cocok.
                                @else
                                    Belum ada catatan stock opname.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($opnames->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $opnames->links() }}
            </div>
        @endif
    </div>
@endsection
