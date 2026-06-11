@extends('layouts.app')

@section('title', 'Pesanan Saya – Kumaw Dimsum')

@section('styles')
<style>
    /* ---- Stats row ---- */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        text-align: center;
    }
    .stat-num { font-size: 1.75rem; font-weight: 800; color: var(--gold); }
    .stat-label { font-size: 0.8125rem; color: var(--muted); margin-top: 0.125rem; }

    /* ---- Order card ---- */
    .order-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        transition: border-color 0.2s;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .order-card:hover { border-color: rgba(245,158,11,0.3); }

    .order-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.875rem;
    }
    .order-number {
        font-size: 0.9375rem;
        font-weight: 700;
        font-family: monospace;
        color: var(--gold);
    }
    .order-date { font-size: 0.8125rem; color: var(--muted); margin-top: 0.2rem; }

    .order-items-preview {
        font-size: 0.875rem;
        color: var(--muted);
        margin-bottom: 0.875rem;
        line-height: 1.5;
    }

    .order-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .order-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        border-radius: 999px;
        font-size: 0.8rem;
        color: var(--muted);
    }

    .order-total { font-weight: 700; font-size: 1rem; color: var(--text); }

    /* Pagination */
    .pagination-nav {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    .page-link {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.875rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--muted);
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .page-link:hover, .page-link.active {
        background: var(--gold-dim);
        border-color: rgba(245,158,11,0.4);
        color: var(--gold);
    }
    .page-link.disabled { opacity: 0.4; pointer-events: none; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🧾 Pesanan Saya</h1>
            <p>Riwayat semua pesanan kamu di Kumaw Dimsum</p>
        </div>
        <a href="{{ route('menu.index') }}" class="btn btn-gold">🥟 Pesan Lagi</a>
    </div>

    @php
        $totalOrders   = $orders->total();
        $activeOrders  = $orders->filter(fn($o) => $o->isActive())->count();
        $completedAll  = $orders->filter(fn($o) => $o->isCompleted())->count();
    @endphp

    {{-- Quick stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num">{{ $totalOrders }}</div>
            <div class="stat-label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $activeOrders }}</div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $completedAll }}</div>
            <div class="stat-label">Selesai (halaman ini)</div>
        </div>
    </div>

    {{-- Order list --}}
    @forelse($orders as $order)
        <div class="order-card" id="order-{{ $order->id }}">
            <div class="order-card-header">
                <div>
                    <div class="order-number">#{{ $order->order_number }}</div>
                    <div class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                <span class="badge badge-{{ $order->status }}">
                    {{ $order->statusLabel }}
                </span>
            </div>

            <div class="order-items-preview">
                @php
                    $preview = $order->items->take(3)->map(fn($i) => $i->menu_name . ' ×' . $i->quantity)->join(', ');
                    $extra   = $order->items->count() - 3;
                @endphp
                {{ $preview }}{{ $extra > 0 ? ", +{$extra} item lainnya" : '' }}
            </div>

            <div class="order-card-footer">
                <div>
                    <span class="order-type-badge">
                        {{ match($order->type) {
                            'dine_in'  => '🍽️ Makan di Tempat',
                            'takeaway' => '📦 Bawa Pulang',
                            'delivery' => '🛵 Delivery',
                            default    => ucfirst($order->type),
                        } }}
                    </span>
                    <span class="order-total" style="margin-left: 0.5rem;">{{ $order->formattedTotal }}</span>
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-ghost" style="padding: 0.25rem 0.75rem; font-size: 0.8125rem;">Detail</a>
                    <form action="{{ route('cart.reorder', $order) }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-gold" style="padding: 0.25rem 0.75rem; font-size: 0.8125rem;">🔄 Pesan Lagi</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="icon">🧾</div>
            <h3>Belum ada pesanan</h3>
            <p>Yuk mulai pesan dimsum favorit kamu!</p>
            <a href="{{ route('menu.index') }}" class="btn btn-gold">Lihat Menu</a>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($orders->hasPages())
        <div class="pagination-nav">
            @if($orders->onFirstPage())
                <span class="page-link disabled">← Sebelumnya</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="page-link">← Sebelumnya</a>
            @endif

            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $orders->currentPage() === $page ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="page-link">Berikutnya →</a>
            @else
                <span class="page-link disabled">Berikutnya →</span>
            @endif
        </div>
    @endif
@endsection
