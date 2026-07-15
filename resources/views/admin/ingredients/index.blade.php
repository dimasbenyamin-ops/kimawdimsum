@extends('layouts.admin')

@section('title', 'Bahan Baku')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.125rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 44px; height: 44px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-icon.gold    { background: var(--gold-dim); color: var(--gold); }
    .stat-icon.error   { background: rgba(248,113,113,0.1); color: var(--error); }
    .stat-icon.success { background: rgba(52,211,153,0.1); color: var(--success); }

    .stat-value { font-size: 1.25rem; font-weight: 700; }
    .stat-label { font-size: 0.75rem; color: var(--muted); }

    .toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .toolbar .search-box {
        flex: 1;
        min-width: 200px;
        max-width: 360px;
        position: relative;
    }

    .toolbar .search-box input {
        padding-left: 2.25rem;
        width: 100%;
    }

    .toolbar .search-box i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 0.875rem;
    }

    .filter-pills {
        display: flex;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .filter-pill {
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        transition: all 0.15s;
    }

    .filter-pill:hover { color: var(--text); border-color: rgba(245,158,11,0.3); }
    .filter-pill.active { background: var(--gold-dim); border-color: var(--gold-border); color: var(--gold); }

    .low-stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.6875rem;
        font-weight: 600;
        background: rgba(248,113,113,0.1);
        border: 1px solid rgba(248,113,113,0.3);
        color: var(--error);
        animation: pulse 2s ease-in-out infinite;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>📦 Bahan Baku</h1>
        <p>Kelola data bahan baku / inventori</p>
    </div>
    <a href="{{ route('admin.ingredients.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg"></i> Tambah Bahan
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="bi bi-box-seam"></i></div>
        <div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-label">Total Bahan Baku</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon error"><i class="bi bi-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-label">Stok Rendah</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-currency-exchange"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
            <div class="stat-label">Nilai Inventori</div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="toolbar">
    <form method="GET" action="{{ route('admin.ingredients.index') }}" class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari bahan baku..." maxlength="100">
        @if($category)<input type="hidden" name="category" value="{{ $category }}">@endif
        @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
    </form>

    <div class="filter-pills">
        <a href="{{ route('admin.ingredients.index') }}"
           class="filter-pill {{ !$filter && !$category ? 'active' : '' }}">Semua</a>
        <a href="{{ route('admin.ingredients.index', ['filter' => 'low_stock']) }}"
           class="filter-pill {{ $filter === 'low_stock' ? 'active' : '' }}">⚠️ Stok Rendah</a>
        @foreach(\App\Models\Ingredient::CATEGORY_LABELS as $key => $label)
            <a href="{{ route('admin.ingredients.index', ['category' => $key]) }}"
               class="filter-pill {{ $category === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th style="text-align:right">Stok</th>
                    <th style="text-align:right">Min. Stok</th>
                    <th style="text-align:right">Biaya Rata²</th>
                    <th style="text-align:right">Nilai</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $item)
                    <tr>
                        <td>
                            <strong>{{ e($item->name) }}</strong>
                            @if($item->isLowStock())
                                <span class="low-stock-badge">⚠ Rendah</span>
                            @endif
                        </td>
                        <td style="color: var(--muted)">{{ e($item->sku ?? '—') }}</td>
                        <td>
                            <span class="badge badge-gold">{{ \App\Models\Ingredient::CATEGORY_LABELS[$item->category] ?? $item->category }}</span>
                        </td>
                        <td style="text-align:right; font-weight:600; {{ $item->isLowStock() ? 'color:var(--error)' : '' }}">
                            {{ number_format($item->current_stock, 2, ',', '.') }}
                            <span style="color:var(--muted);font-size:0.75rem">{{ e($item->unit?->abbreviation ?? '') }}</span>
                        </td>
                        <td style="text-align:right; color:var(--muted)">
                            {{ number_format($item->min_stock, 2, ',', '.') }}
                            <span style="font-size:0.75rem">{{ e($item->unit?->abbreviation ?? '') }}</span>
                        </td>
                        <td style="text-align:right">{{ $item->formatted_avg_cost }}/{{ e($item->unit?->abbreviation ?? '') }}</td>
                        <td style="text-align:right; font-weight:600">
                            Rp {{ number_format($item->stock_value, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-muted">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:0.375rem;justify-content:center">
                                <a href="{{ route('admin.ingredients.edit', $item) }}" class="btn btn-ghost btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.ingredients.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus bahan baku &quot;{{ e($item->name) }}&quot;?')">
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
                        <td colspan="9" style="text-align:center;padding:2rem;color:var(--muted)">
                            <div style="font-size:2rem;margin-bottom:0.5rem">📦</div>
                            Belum ada bahan baku.
                            <a href="{{ route('admin.ingredients.create') }}" style="color:var(--gold)">Tambah sekarang →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($ingredients->hasPages())
    <div style="margin-top:1.25rem">
        {{ $ingredients->links() }}
    </div>
@endif
@endsection
