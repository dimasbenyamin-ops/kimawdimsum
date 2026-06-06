@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' – Kumaw Dimsum')

@section('styles')
<style>
    .order-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ---- Status timeline ---- */
    .timeline {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 2rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        min-width: 80px;
        position: relative;
    }
    .timeline-step::before {
        content: '';
        position: absolute;
        top: 16px;
        left: calc(-50% + 16px);
        width: calc(100% - 32px);
        height: 2px;
        background: var(--border);
        z-index: 0;
    }
    .timeline-step:first-child::before { display: none; }

    .timeline-dot {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--bg2);
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        position: relative;
        z-index: 1;
        transition: all 0.3s;
    }
    .timeline-step.done .timeline-dot {
        background: rgba(52,211,153,0.15);
        border-color: var(--success);
        color: var(--success);
    }
    .timeline-step.active .timeline-dot {
        background: var(--gold-dim);
        border-color: var(--gold);
        color: var(--gold);
        box-shadow: 0 0 0 4px rgba(245,158,11,0.15);
    }
    .timeline-step.done::before,
    .timeline-step.active::before {
        background: var(--success);
    }
    .timeline-label {
        font-size: 0.6875rem;
        font-weight: 500;
        color: var(--muted);
        margin-top: 0.375rem;
        text-align: center;
    }
    .timeline-step.done .timeline-label,
    .timeline-step.active .timeline-label { color: var(--text); }

    /* ---- Item rows ---- */
    .item-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 0;
        border-bottom: 1px solid var(--border);
    }
    .item-row:last-child { border-bottom: none; }

    .item-emoji {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(99,102,241,0.1));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .item-name  { font-weight: 600; font-size: 0.9rem; }
    .item-notes { font-size: 0.8rem; color: var(--muted); font-style: italic; }
    .item-qty   { font-size: 0.8125rem; color: var(--muted); margin-top: 0.125rem; }
    .item-price { margin-left: auto; font-weight: 700; font-size: 0.9375rem; color: var(--gold); flex-shrink: 0; }

    /* ---- Info card ---- */
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        position: sticky;
        top: 80px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.9rem;
        border-bottom: 1px solid var(--border);
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: var(--muted); }
    .info-row .value { font-weight: 500; }
    .info-row.total .value { color: var(--gold); font-size: 1rem; font-weight: 700; }

    @media (max-width: 768px) {
        .order-layout { grid-template-columns: 1fr; }
        .info-card { position: static; }
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🧾 Detail Pesanan</h1>
            <p style="font-family:monospace;color:var(--gold);">#{{ $order->order_number }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-ghost">← Semua Pesanan</a>
    </div>

    {{-- Status timeline --}}
    @php
        $allStatuses = [
            ['key' => 'pending_payment', 'icon' => '💳', 'label' => 'Belum Bayar'],
            ['key' => 'confirmed',       'icon' => '✅', 'label' => 'Dikonfirmasi'],
            ['key' => 'preparing',       'icon' => '👨‍🍳', 'label' => 'Diproses'],
            ['key' => 'ready',           'icon' => '🔔', 'label' => 'Siap'],
            ['key' => 'completed',       'icon' => '🎉', 'label' => 'Selesai'],
        ];
        $statusOrder = ['pending_payment','confirmed','preparing','ready','completed'];
        $currentIdx  = array_search($order->status, $statusOrder);
        if ($currentIdx === false) $currentIdx = 0; // fallback for unknown
        $cancelled   = $order->isCancelled();
    @endphp

    @if(!$cancelled)
        <div class="card" style="margin-bottom:1.5rem">
            <div class="timeline">
                @foreach($allStatuses as $i => $step)
                    @php
                        $idx = array_search($step['key'], $statusOrder);
                        $cls = $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'active' : '');
                    @endphp
                    <div class="timeline-step {{ $cls }}">
                        <div class="timeline-dot">{{ $step['icon'] }}</div>
                        <div class="timeline-label">{{ $step['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-error" style="margin-bottom:1.5rem">
            ❌ Pesanan ini dibatalkan.
            @if($order->cashier_notes) — {{ $order->cashier_notes }} @endif
        </div>
    @endif

    <div class="order-layout">
        {{-- Items --}}
        <div class="card">
            <div class="section-title">🥟 Item yang Dipesan</div>

            @foreach($order->items as $item)
                <div class="item-row">
                    <div class="item-emoji">
                        {{ match($item->menu_category ?? '') {
                            'siomay'  => '🥟',
                            'hakau'   => '🦐',
                            'lumpia'  => '🌯',
                            'bao'     => '🫓',
                            'shumai'  => '🍢',
                            'minuman' => '🍵',
                            default   => '✨',
                        } }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="item-name">{{ $item->menu_name }}</div>
                        @if($item->notes)
                            <div class="item-notes">📝 {{ $item->notes }}</div>
                        @endif
                        <div class="item-qty">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}
                        </div>
                    </div>
                    <div class="item-price">
                        Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Info sidebar --}}
        <div class="info-card">
            <div class="section-title">📋 Info Pesanan</div>

            <div class="info-row">
                <span class="label">Status</span>
                <span class="badge badge-{{ $order->status }}">{{ $order->statusLabel }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jenis</span>
                <span class="value">
                    {{ match($order->type) {
                        'dine_in'  => '🍽️ Makan di Tempat',
                        'takeaway' => '📦 Bawa Pulang',
                        'delivery' => '🛵 Delivery',
                        default    => ucfirst($order->type),
                    } }}
                </span>
            </div>
            @if($order->table_number)
                <div class="info-row">
                    <span class="label">Meja</span>
                    <span class="value">No. {{ $order->table_number }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="label">Waktu</span>
                <span class="value" style="font-size:0.8125rem">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            @if($order->customer_notes)
                <div class="info-row">
                    <span class="label">Catatan</span>
                    <span class="value" style="font-size:0.8125rem;font-style:italic">{{ $order->customer_notes }}</span>
                </div>
            @endif

            <div style="height:1rem"></div>
            <div class="section-title">💰 Pembayaran</div>

            <div class="info-row">
                <span class="label">Subtotal</span>
                <span class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Pajak (11%)</span>
                <span class="value">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>
            <div class="info-row total">
                <span class="label" style="color:var(--text);font-weight:700">Total</span>
                <span class="value">{{ $order->formattedTotal }}</span>
            </div>
            @if($order->isPaid())
                <div class="info-row">
                    <span class="label">Dibayar</span>
                    <span class="value" style="color:var(--success)">
                        {{ strtoupper($order->payment_method) }} – {{ $order->paid_at->format('H:i') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
@endsection
