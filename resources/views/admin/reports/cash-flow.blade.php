@extends('layouts.admin')

@section('title', 'Laporan Arus Kas – Kumaw Dimsum Admin')

@section('styles')
<style>
    .report-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .report-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px dashed var(--border);
    }
    .report-row:last-child {
        border-bottom: none;
    }
    .report-row.header {
        font-weight: 700;
        font-size: 1.1rem;
        border-bottom: 2px solid var(--border);
        padding-top: 1.5rem;
    }
    .report-row.total {
        font-weight: 700;
        font-size: 1.25rem;
        border-top: 2px solid var(--border);
        border-bottom: none;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>💸 Laporan Arus Kas (Cash Flow)</h1>
            <p>Pergerakan uang tunai masuk (penjualan) dan keluar (pembelian bahan & biaya operasional).</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <div style="padding:1.5rem;">
            <form method="GET" action="{{ route('admin.reports.cash-flow') }}" style="display:flex; gap:1rem; align-items:flex-end;">
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

    <div class="report-card">
        <h2 style="font-size: 1.5rem; text-align: center; margin-bottom: 0.5rem;">Kumaw Dimsum</h2>
        <div style="text-align: center; color: var(--muted); margin-bottom: 2rem;">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            <!-- Arus Kas Masuk -->
            <div class="report-row header">
                <div>Arus Kas Masuk (Inflow)</div>
            </div>
            <div class="report-row">
                <div>Penerimaan Penjualan (POS)</div>
                <div style="font-family:monospace" class="text-success">Rp {{ number_format($inflow, 2, ',', '.') }}</div>
            </div>
            <div class="report-row" style="font-weight: 600; background: var(--surface-hover);">
                <div>Total Arus Kas Masuk</div>
                <div style="font-family:monospace" class="text-success">Rp {{ number_format($inflow, 2, ',', '.') }}</div>
            </div>

            <!-- Arus Kas Keluar -->
            <div class="report-row header">
                <div>Arus Kas Keluar (Outflow)</div>
            </div>
            <div class="report-row">
                <div>Pembayaran Pembelian Bahan Baku (Confirmed PO)</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($purchases, 2, ',', '.') }})</div>
            </div>
            <div class="report-row">
                <div>Pembayaran Biaya Operasional (OpEx)</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($expenses, 2, ',', '.') }})</div>
            </div>
            <div class="report-row" style="font-weight: 600; background: var(--surface-hover);">
                <div>Total Arus Kas Keluar</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($totalOutflow, 2, ',', '.') }})</div>
            </div>

            <!-- Net Cash Flow -->
            <div class="report-row total" style="{{ $netCashFlow >= 0 ? 'color:var(--success)' : 'color:var(--danger)' }}; font-size: 1.5rem; margin-top:2rem">
                <div>ARUS KAS BERSIH (Net Cash Flow)</div>
                <div style="font-family:monospace">
                    {{ $netCashFlow < 0 ? '(' : '' }}Rp {{ number_format(abs($netCashFlow), 2, ',', '.') }}{{ $netCashFlow < 0 ? ')' : '' }}
                </div>
            </div>
        </div>
    </div>
@endsection
