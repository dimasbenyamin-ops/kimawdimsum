@extends('layouts.admin')

@section('title', 'Pembelian Bahan Baku – Kumaw X Atmosphr Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>🛒 Pembelian Bahan Baku</h1>
            <p>Kelola *Purchase Order* (PO) dan stok masuk dari supplier.</p>
        </div>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Buat PO Baru
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.purchases.index') }}" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO..." class="form-control" style="max-width:250px">
                
                <select name="status" class="form-control" style="max-width:150px" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Cari</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nomor PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th style="text-align:right">Total (Rp)</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $index => $purchase)
                        <tr>
                            <td style="color:var(--muted)">{{ $purchases->firstItem() + $index }}</td>
                            <td>
                                <div style="font-weight: 500; color:var(--text)">{{ $purchase->purchase_number }}</div>
                            </td>
                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                            <td style="text-align:right; font-family:monospace">{{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($purchase->status === 'draft')
                                    <span class="badge" style="background:#fef3c7; color:#92400e">Draft</span>
                                @elseif($purchase->status === 'confirmed')
                                    <span class="badge" style="background:#d1fae5; color:#065f46">Confirmed</span>
                                @else
                                    <span class="badge" style="background:#fee2e2; color:#991b1b">Cancelled</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-sm btn-ghost" title="Detail PO">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 3rem; color:var(--muted)">
                                @if($search || $status)
                                    Tidak ada data pembelian yang cocok.
                                @else
                                    Belum ada data pembelian.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
@endsection
