@extends('layouts.admin')

@section('title', 'Detail PO ' . $purchase->purchase_number . ' – Kumaw Dimsum Admin')

@section('styles')
<style>
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    .info-item label {
        font-size: 0.875rem;
        color: var(--muted);
        margin-bottom: 0.25rem;
        display: block;
    }
    .info-item div {
        font-weight: 500;
        color: var(--text);
    }
</style>
@endsection

@section('content')
    <div class="page-header" style="align-items:flex-start">
        <div>
            <h1>🛒 Detail Pembelian</h1>
            <p>{{ $purchase->purchase_number }}</p>
        </div>
        <div style="display:flex; gap:1rem">
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">← Kembali</a>
            
            @if($purchase->status === 'draft')
                <form action="{{ route('admin.purchases.status', $purchase) }}" method="POST" onsubmit="return confirm('Konfirmasi pembelian? Stok bahan baku akan otomatis bertambah dan HPP akan dihitung ulang. Aksi ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-check-circle"></i> Konfirmasi Pembelian (Terima Barang)
                    </button>
                </form>

                <form action="{{ route('admin.purchases.status', $purchase) }}" method="POST" onsubmit="return confirm('Batalkan PO ini?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Batalkan PO
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($purchase->status === 'confirmed')
        <div style="background:#d1fae5; color:#065f46; padding:1rem 1.5rem; border-radius:var(--radius); margin-bottom:2rem; display:flex; align-items:center; gap:0.75rem;">
            <i class="bi bi-check-circle-fill" style="font-size:1.25rem"></i>
            <div>Pembelian ini telah dikonfirmasi. Stok bahan baku sudah masuk dan HPP (Average Cost) telah diperbarui otomatis.</div>
        </div>
    @elseif($purchase->status === 'cancelled')
        <div style="background:#fee2e2; color:#991b1b; padding:1rem 1.5rem; border-radius:var(--radius); margin-bottom:2rem; display:flex; align-items:center; gap:0.75rem;">
            <i class="bi bi-x-circle-fill" style="font-size:1.25rem"></i>
            <div>Pembelian ini telah dibatalkan.</div>
        </div>
    @endif

    <div class="info-card">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Informasi PO</h2>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Nomor PO</label>
                <div>{{ $purchase->purchase_number }}</div>
            </div>
            <div class="info-item">
                <label>Tanggal Pembelian</label>
                <div>{{ $purchase->purchase_date->format('d F Y') }}</div>
            </div>
            <div class="info-item">
                <label>Supplier</label>
                <div>{{ $purchase->supplier->name ?? 'Tanpa Supplier' }}</div>
            </div>
            <div class="info-item">
                <label>Status</label>
                <div>
                    @if($purchase->status === 'draft')
                        <span class="badge" style="background:#fef3c7; color:#92400e">Draft (Belum Diterima)</span>
                    @elseif($purchase->status === 'confirmed')
                        <span class="badge" style="background:#d1fae5; color:#065f46">Confirmed (Selesai)</span>
                    @else
                        <span class="badge" style="background:#fee2e2; color:#991b1b">Cancelled</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <label>Dibuat Oleh</label>
                <div>{{ $purchase->creator->name ?? 'System' }}</div>
            </div>
        </div>

        @if($purchase->notes)
            <div class="info-item" style="margin-top: 1.5rem">
                <label>Catatan</label>
                <div>{{ $purchase->notes }}</div>
            </div>
        @endif
    </div>

    <div class="info-card">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Daftar Barang Diterima</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bahan Baku</th>
                        <th style="text-align:right">Kuantitas</th>
                        <th style="text-align:right">Harga Satuan (Rp)</th>
                        <th style="text-align:right">Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                        <tr>
                            <td style="color:var(--muted)">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 500">{{ $item->ingredient->name }}</div>
                            </td>
                            <td style="text-align:right">
                                {{ number_format($item->quantity, 4, ',', '.') }} {{ $item->unit->abbreviation ?? '' }}
                            </td>
                            <td style="text-align:right; font-family:monospace">
                                {{ number_format($item->unit_price, 2, ',', '.') }}
                            </td>
                            <td style="text-align:right; font-family:monospace">
                                {{ number_format($item->subtotal, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--surface-hover); font-weight:bold;">
                        <td colspan="4" style="text-align:right">Total Pembelian :</td>
                        <td style="text-align:right; font-family:monospace; font-size:1.2rem; color:var(--gold)">
                            Rp {{ number_format($purchase->total_amount, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
