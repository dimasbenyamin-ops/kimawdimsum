@extends('layouts.admin')

@section('title', 'Detail SO ' . $stockOpname->opname_number . ' – Kumaw Dimsum Admin')

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
            <h1>📦 Detail Stock Opname</h1>
            <p>{{ $stockOpname->opname_number }}</p>
        </div>
        <div style="display:flex; gap:1rem">
            <a href="{{ route('admin.stock-opnames.index') }}" class="btn btn-ghost">← Kembali</a>
            
            @if($stockOpname->status === 'draft')
                <form action="{{ route('admin.stock-opnames.status', $stockOpname) }}" method="POST" onsubmit="return confirm('Konfirmasi Stock Opname? Stok sistem akan diperbarui menyesuaikan stok fisik. Aksi ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-check-circle"></i> Konfirmasi SO
                    </button>
                </form>

                <form action="{{ route('admin.stock-opnames.status', $stockOpname) }}" method="POST" onsubmit="return confirm('Batalkan Stock Opname ini?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Batalkan SO
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($stockOpname->status === 'confirmed')
        <div style="background:#d1fae5; color:#065f46; padding:1rem 1.5rem; border-radius:var(--radius); margin-bottom:2rem; display:flex; align-items:center; gap:0.75rem;">
            <i class="bi bi-check-circle-fill" style="font-size:1.25rem"></i>
            <div>Stock Opname ini telah dikonfirmasi. Stok sistem sudah diperbarui.</div>
        </div>
    @elseif($stockOpname->status === 'cancelled')
        <div style="background:#fee2e2; color:#991b1b; padding:1rem 1.5rem; border-radius:var(--radius); margin-bottom:2rem; display:flex; align-items:center; gap:0.75rem;">
            <i class="bi bi-x-circle-fill" style="font-size:1.25rem"></i>
            <div>Stock Opname ini telah dibatalkan.</div>
        </div>
    @endif

    <div class="info-card">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Informasi SO</h2>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Nomor SO</label>
                <div>{{ $stockOpname->opname_number }}</div>
            </div>
            <div class="info-item">
                <label>Tanggal</label>
                <div>{{ $stockOpname->opname_date->format('d F Y') }}</div>
            </div>
            <div class="info-item">
                <label>Status</label>
                <div>
                    @if($stockOpname->status === 'draft')
                        <span class="badge" style="background:#fef3c7; color:#92400e">Draft</span>
                    @elseif($stockOpname->status === 'confirmed')
                        <span class="badge" style="background:#d1fae5; color:#065f46">Confirmed</span>
                    @else
                        <span class="badge" style="background:#fee2e2; color:#991b1b">Cancelled</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <label>Dibuat Oleh</label>
                <div>{{ $stockOpname->creator->name ?? 'System' }}</div>
            </div>
        </div>

        @if($stockOpname->notes)
            <div class="info-item" style="margin-top: 1.5rem">
                <label>Catatan</label>
                <div>{{ $stockOpname->notes }}</div>
            </div>
        @endif
    </div>

    <div class="info-card">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Analisis Selisih (Variance)</h2>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th style="text-align:right">Stok Sistem</th>
                        <th style="text-align:right">Stok Fisik</th>
                        <th style="text-align:right">Selisih</th>
                        <th style="text-align:right">Estimasi Cost (Rp)</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalVarianceCost = 0; @endphp
                    @foreach($stockOpname->items as $item)
                        @php $totalVarianceCost += $item->variance_cost; @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 500">{{ $item->ingredient->name }}</div>
                            </td>
                            <td style="text-align:right; color:var(--muted)">
                                {{ number_format($item->system_stock, 4, ',', '.') }} {{ $item->ingredient->unit->abbreviation ?? '' }}
                            </td>
                            <td style="text-align:right; font-weight:600">
                                {{ number_format($item->physical_stock, 4, ',', '.') }} {{ $item->ingredient->unit->abbreviation ?? '' }}
                            </td>
                            <td style="text-align:right">
                                @if($item->variance < 0)
                                    <span style="color:var(--danger)">{{ number_format($item->variance, 4, ',', '.') }}</span>
                                @elseif($item->variance > 0)
                                    <span style="color:var(--success)">+{{ number_format($item->variance, 4, ',', '.') }}</span>
                                @else
                                    <span style="color:var(--muted)">0</span>
                                @endif
                            </td>
                            <td style="text-align:right; font-family:monospace">
                                @if($item->variance_cost < 0)
                                    <span style="color:var(--danger)">{{ number_format($item->variance_cost, 2, ',', '.') }}</span>
                                @elseif($item->variance_cost > 0)
                                    <span style="color:var(--success)">+{{ number_format($item->variance_cost, 2, ',', '.') }}</span>
                                @else
                                    <span style="color:var(--muted)">0</span>
                                @endif
                            </td>
                            <td style="color:var(--muted); font-size:0.875rem">{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--surface-hover); font-weight:bold;">
                        <td colspan="4" style="text-align:right">Total Net Variance Cost :</td>
                        <td style="text-align:right; font-family:monospace; font-size:1.1rem; color: {{ $totalVarianceCost < 0 ? 'var(--danger)' : ($totalVarianceCost > 0 ? 'var(--success)' : 'var(--text)') }}">
                            Rp {{ number_format($totalVarianceCost, 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
