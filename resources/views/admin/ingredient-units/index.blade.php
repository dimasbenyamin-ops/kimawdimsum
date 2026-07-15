@extends('layouts.admin')

@section('title', 'Satuan Bahan')

@section('content')
<div class="page-header">
    <div>
        <h1>📏 Satuan Bahan</h1>
        <p>Kelola satuan pengukuran bahan baku (gram, kg, liter, pcs, dll)</p>
    </div>
    <a href="{{ route('admin.ingredient-units.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg"></i> Tambah Satuan
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
@endif

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Singkatan</th>
                    <th>Satuan Dasar</th>
                    <th style="text-align:right">Faktor Konversi</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                    <tr>
                        <td><strong>{{ e($unit->name) }}</strong></td>
                        <td>
                            <span class="badge badge-gold">{{ e($unit->abbreviation) }}</span>
                        </td>
                        <td>
                            @if($unit->baseUnit)
                                {{ e($unit->baseUnit->name) }} ({{ e($unit->baseUnit->abbreviation) }})
                            @else
                                <span style="color:var(--muted)">— Satuan Dasar —</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            @if($unit->baseUnit)
                                1 {{ e($unit->abbreviation) }} = {{ number_format($unit->conversion_factor, 2, ',', '.') }} {{ e($unit->baseUnit->abbreviation) }}
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:0.375rem;justify-content:center">
                                <a href="{{ route('admin.ingredient-units.edit', $unit) }}" class="btn btn-ghost btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.ingredient-units.destroy', $unit) }}"
                                      onsubmit="return confirm('Hapus satuan &quot;{{ e($unit->name) }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">
                            <div style="font-size:2rem;margin-bottom:0.5rem">📏</div>
                            Belum ada satuan.
                            <a href="{{ route('admin.ingredient-units.create') }}" style="color:var(--gold)">Tambah sekarang →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
