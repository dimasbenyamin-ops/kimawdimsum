@extends('layouts.admin')

@section('title', 'Rekap Pendapatan')

@section('content')
    <div class="page-header">
        <div>
            <h1>📊 Rekap Pendapatan</h1>
            <p>Laporan pendapatan berdasarkan pesanan yang telah selesai</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body">
            <form action="{{ route('admin.reports.revenue') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                    <label for="end_date">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control" required>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-gold">Filter</button>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-ghost">✕ Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="card" style="border-left: 4px solid var(--gold);">
            <div class="card-body">
                <p style="font-size: 0.8125rem; color: var(--muted); text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Total Pendapatan</p>
                <h3 style="font-size: 1.75rem; font-weight: 700; color: var(--text);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid var(--info);">
            <div class="card-body">
                <p style="font-size: 0.8125rem; color: var(--muted); text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Pesanan Selesai</p>
                <h3 style="font-size: 1.75rem; font-weight: 700; color: var(--text);">{{ $totalOrders }}</h3>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid var(--success);">
            <div class="card-body">
                <p style="font-size: 0.8125rem; color: var(--muted); text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Rata-rata Pesanan</p>
                <h3 style="font-size: 1.75rem; font-weight: 700; color: var(--text);">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>No. Telp</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span style="font-family: monospace; font-weight: 600; color: var(--gold);">{{ $order->order_number }}</span></td>
                                <td>{{ $order->customer_name ?? 'Guest' }}</td>
                                <td>{{ $order->phone_number ?? '-' }}</td>
                                <td>{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</td>
                                <td>
                                    @php
                                        $pmLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'unpaid' => 'Belum Bayar'];
                                    @endphp
                                    <span class="badge badge-muted">{{ $pmLabels[$order->payment_method] ?? $order->payment_method }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem 0; color: var(--muted);">
                                    Tidak ada data pendapatan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
