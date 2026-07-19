@extends('layouts.admin')

@section('title', 'Laporan HPP & Margin Menu – Kumaw X Atmosphr Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>🍳 Analisis HPP & Margin</h1>
            <p>Cost of Goods Sold (COGS) dan margin keuntungan per menu berdasarkan resep.</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Menu</th>
                        <th style="text-align:right">Harga Jual (Rp)</th>
                        <th style="text-align:right">Estimasi HPP (Rp)</th>
                        <th style="text-align:right">Profit per Porsi (Rp)</th>
                        <th style="text-align:center">Gross Margin (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuCogs as $index => $item)
                        <tr>
                            <td style="color:var(--muted)">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 500">{{ $item['name'] }}</div>
                            </td>
                            <td style="text-align:right; font-family:monospace">
                                {{ number_format($item['price'], 2, ',', '.') }}
                            </td>
                            <td style="text-align:right; font-family:monospace; color:var(--danger)">
                                {{ number_format($item['cost'], 2, ',', '.') }}
                            </td>
                            <td style="text-align:right; font-family:monospace; color:var(--success); font-weight:bold">
                                {{ number_format($item['profit'], 2, ',', '.') }}
                            </td>
                            <td style="text-align:center">
                                @if($item['margin'] > 50)
                                    <span class="badge" style="background:#d1fae5; color:#065f46">{{ $item['margin'] }}%</span>
                                @elseif($item['margin'] > 30)
                                    <span class="badge" style="background:#fef3c7; color:#92400e">{{ $item['margin'] }}%</span>
                                @else
                                    <span class="badge" style="background:#fee2e2; color:#991b1b">{{ $item['margin'] }}%</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
