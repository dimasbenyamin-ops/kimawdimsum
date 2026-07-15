@extends('layouts.admin')

@section('title', 'Pencatatan Waste / Spoilage – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>🗑️ Pencatatan Waste (Spoilage)</h1>
            <p>Catat bahan baku yang terbuang karena basi, rusak, atau alasan lain.</p>
        </div>
        <a href="{{ route('admin.waste.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Catat Waste Baru
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.waste.index') }}" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Bahan Baku..." class="form-control" style="max-width:250px">
                
                <select name="reason" class="form-control" style="max-width:200px" onchange="this.form.submit()">
                    <option value="">Semua Alasan</option>
                    <option value="expired" {{ request('reason') === 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                    <option value="damaged" {{ request('reason') === 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                    <option value="sample" {{ request('reason') === 'sample' ? 'selected' : '' }}>Sampel / Tester</option>
                    <option value="production_loss" {{ request('reason') === 'production_loss' ? 'selected' : '' }}>Gagal Produksi</option>
                    <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>

                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Cari</button>
                @if(request('search') || request('reason'))
                    <a href="{{ route('admin.waste.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Tanggal</th>
                        <th>Bahan Baku</th>
                        <th style="text-align:right">Kuantitas</th>
                        <th>Alasan</th>
                        <th style="text-align:right">Estimasi Rugi (Rp)</th>
                        <th>Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wasteLogs as $index => $log)
                        <tr>
                            <td style="color:var(--muted)">{{ $wasteLogs->firstItem() + $index }}</td>
                            <td>{{ $log->waste_date->format('d M Y') }}</td>
                            <td>
                                <div style="font-weight: 500; color:var(--text)">{{ $log->ingredient->name ?? '-' }}</div>
                            </td>
                            <td style="text-align:right">
                                <span style="color:var(--danger)">
                                    -{{ number_format($log->quantity, 4, ',', '.') }} {{ $log->unit->abbreviation ?? '' }}
                                </span>
                            </td>
                            <td>
                                @if($log->reason === 'expired')
                                    <span class="badge" style="background:#fee2e2; color:#991b1b">Expired</span>
                                @elseif($log->reason === 'damaged')
                                    <span class="badge" style="background:#ffedd5; color:#9a3412">Damaged</span>
                                @elseif($log->reason === 'sample')
                                    <span class="badge" style="background:#dbeafe; color:#1e40af">Sample</span>
                                @elseif($log->reason === 'production_loss')
                                    <span class="badge" style="background:#f3e8ff; color:#6b21a8">Production Loss</span>
                                @else
                                    <span class="badge" style="background:#f3f4f6; color:#374151">Other</span>
                                @endif
                                
                                @if($log->notes)
                                    <div style="font-size:0.8rem; color:var(--muted); margin-top:0.25rem">{{ $log->notes }}</div>
                                @endif
                            </td>
                            <td style="text-align:right; font-family:monospace">
                                {{ number_format($log->cost_amount, 2, ',', '.') }}
                            </td>
                            <td style="color:var(--muted)">{{ $log->creator->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 3rem; color:var(--muted)">
                                @if($search || $reason)
                                    Tidak ada data waste yang cocok.
                                @else
                                    Belum ada catatan waste.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wasteLogs->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $wasteLogs->links() }}
            </div>
        @endif
    </div>
@endsection
