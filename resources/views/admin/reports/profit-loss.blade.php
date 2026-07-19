@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi – Kumaw X Atmosphr Admin')

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
        font-size: 1rem;
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
        font-size: 1.5rem;
        border-top: 2px solid var(--border);
        border-bottom: none;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }
    
    .report-title {
        font-size: 1.5rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 640px) {
        .report-card {
            padding: 1rem;
        }
        .report-title {
            font-size: 1.2rem;
        }
        .report-row {
            flex-direction: column;
            gap: 0.25rem;
            font-size: 0.85rem;
        }
        .report-row.header {
            font-size: 0.95rem;
        }
        .report-row.total {
            font-size: 1.1rem;
            margin-top: 1rem;
        }
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>📈 Laporan Laba Rugi (P&L)</h1>
            <p>Profit & Loss Statement berdasarkan aktivitas penjualan dan pengeluaran.</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <div style="padding:1.5rem;">
            <form method="GET" action="{{ route('admin.reports.profit-loss') }}" class="filter-form">
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
        <h2 class="report-title">Kumaw X Atmosphr</h2>
        <div style="text-align: center; color: var(--muted); margin-bottom: 2rem; font-size: 0.9rem;">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            <!-- Pendapatan -->
            <div class="report-row header">
                <div>Pendapatan (Revenue)</div>
            </div>
            <div class="report-row">
                <div>Penjualan Kotor (POS)</div>
                <div style="font-family:monospace">Rp {{ number_format($revenue, 2, ',', '.') }}</div>
            </div>
            <div class="report-row" style="font-weight: 600; background: var(--surface-hover); padding: 0.75rem;">
                <div>Total Pendapatan</div>
                <div style="font-family:monospace">Rp {{ number_format($revenue, 2, ',', '.') }}</div>
            </div>

            <!-- Harga Pokok Penjualan -->
            <div class="report-row header">
                <div>Harga Pokok Penjualan (COGS)</div>
            </div>
            <div class="report-row">
                <div>Biaya Bahan Baku Terjual (Estimasi)</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($cogs, 2, ',', '.') }})</div>
            </div>
            <div class="report-row" style="font-weight: 600; background: var(--surface-hover); padding: 0.75rem;">
                <div>Total COGS</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($cogs, 2, ',', '.') }})</div>
            </div>

            <!-- Laba Kotor -->
            <div class="report-row total" style="color:var(--gold)">
                <div>LABA KOTOR (Gross Profit)</div>
                <div style="font-family:monospace">Rp {{ number_format($grossProfit, 2, ',', '.') }}</div>
            </div>

            <!-- Beban Operasional -->
            <div class="report-row header">
                <div>Beban Operasional (OpEx) & Lain-lain</div>
            </div>
            <div class="report-row">
                <div>Biaya Operasional Tercatat</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($expenses, 2, ',', '.') }})</div>
            </div>
            <div class="report-row">
                <div>Kerugian Waste (Spoilage)</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($wasteCost, 2, ',', '.') }})</div>
            </div>
            <div class="report-row" style="font-weight: 600; background: var(--surface-hover); padding: 0.75rem;">
                <div>Total Beban & Kerugian</div>
                <div style="font-family:monospace" class="text-danger">(Rp {{ number_format($expenses + $wasteCost, 2, ',', '.') }})</div>
            </div>

            <!-- Laba Bersih -->
            <div class="report-row total" style="{{ $netProfit >= 0 ? 'color:var(--success)' : 'color:var(--danger)' }}; margin-top:2rem;">
                <div>LABA BERSIH (Net Profit)</div>
                <div style="font-family:monospace">Rp {{ number_format($netProfit, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
@endsection
