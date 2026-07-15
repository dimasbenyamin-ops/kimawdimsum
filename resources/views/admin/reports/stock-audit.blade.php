@extends('layouts.admin')

@section('title', 'Valuasi & Audit Stok Gudang – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>📦 Valuasi Stok Gudang</h1>
            <p>Estimasi nilai aset bahan baku yang ada di gudang saat ini berdasarkan HPP.</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem; padding: 2rem; background: var(--surface-hover); text-align: center;">
        <h2 style="font-size: 1rem; color: var(--muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px;">Total Nilai Aset Bahan Baku</h2>
        <div style="font-size: 2.5rem; font-weight: 800; color: var(--gold); font-family: monospace;">
            Rp {{ number_format($totalStockValue, 2, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Bahan Baku</th>
                        <th style="text-align:right">Stok Tersedia</th>
                        <th style="text-align:right">HPP / Unit (Rp)</th>
                        <th style="text-align:right">Nilai Aset (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingredients as $index => $ingredient)
                        @php $stockValue = $ingredient->current_stock * $ingredient->avg_cost; @endphp
                        <tr>
                            <td style="color:var(--muted)">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 500">{{ $ingredient->name }}</div>
                            </td>
                            <td style="text-align:right">
                                {{ number_format($ingredient->current_stock, 4, ',', '.') }} {{ $ingredient->unit->abbreviation ?? '' }}
                            </td>
                            <td style="text-align:right; font-family:monospace; color:var(--muted)">
                                {{ number_format($ingredient->avg_cost, 2, ',', '.') }}
                            </td>
                            <td style="text-align:right; font-family:monospace; font-weight:600">
                                {{ number_format($stockValue, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
