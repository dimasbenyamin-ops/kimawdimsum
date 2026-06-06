@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* ── Welcome Banner ───────────────────────────────────────── */
    .welcome-banner {
        position: relative;
        border-radius: 20px;
        padding: 2rem 2rem 2rem 2.25rem;
        background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(37,99,235,0.10) 50%, rgba(167,139,250,0.08) 100%);
        border: 1px solid rgba(245,158,11,0.18);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 65%);
        pointer-events: none;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 40%;
        width: 180px; height: 180px;
        background: radial-gradient(circle, rgba(37,99,235,0.1) 0%, transparent 65%);
        pointer-events: none;
    }

    .welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--gold);
        background: var(--gold-dim);
        border: 1px solid var(--gold-border);
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        margin-bottom: 0.75rem;
    }

    .welcome-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
        margin-bottom: 0.4rem;
    }

    .welcome-name span {
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-role {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #a78bfa;
        background: rgba(167,139,250,0.1);
        border: 1px solid rgba(167,139,250,0.2);
        border-radius: 20px;
        padding: 0.3rem 0.85rem;
        margin-top: 0.25rem;
    }

    .welcome-date {
        font-size: 0.82rem;
        color: var(--muted);
        margin-top: 0.75rem;
    }

    /* ── Quick-access cards grid ────────────────────────────── */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .quick-card {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem 1.25rem 1rem;
        text-decoration: none;
        color: var(--text);
        transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        overflow: hidden;
    }

    .quick-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 16px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .quick-card:hover {
        transform: translateY(-3px);
        text-decoration: none;
        color: var(--text);
    }

    .quick-card:hover::before { opacity: 1; }

    .quick-card.blue   { --accent: #3b82f6; --accent-bg: rgba(59,130,246,0.08); }
    .quick-card.gold   { --accent: #f59e0b; --accent-bg: rgba(245,158,11,0.08); }
    .quick-card.purple { --accent: #a78bfa; --accent-bg: rgba(167,139,250,0.08); }
    .quick-card.green  { --accent: #34d399; --accent-bg: rgba(52,211,153,0.08); }
    .quick-card.red    { --accent: #f87171; --accent-bg: rgba(248,113,113,0.08); }
    .quick-card.indigo { --accent: #818cf8; --accent-bg: rgba(129,140,248,0.08); }

    .quick-card:hover { border-color: var(--accent); box-shadow: 0 8px 24px rgba(0,0,0,0.25); }
    .quick-card::before { background: var(--accent-bg); }

    .quick-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: var(--accent-bg);
        color: var(--accent);
        border: 1px solid rgba(255,255,255,0.06);
        flex-shrink: 0;
    }

    .quick-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text);
        line-height: 1.3;
    }

    .quick-sub {
        font-size: 0.75rem;
        color: var(--muted);
        margin-top: 0.15rem;
    }

    .quick-arrow {
        margin-top: auto;
        font-size: 0.75rem;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 0.25rem;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity 0.2s, transform 0.2s;
    }

    .quick-card:hover .quick-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* ── Info section ────────────────────────────────────────── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr 1fr; }
        .welcome-name { font-size: 1.35rem; }
    }

    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .info-card-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card-title i { color: var(--gold); }

    /* system info rows */
    .sys-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.875rem;
    }
    .sys-row:last-child { border-bottom: none; padding-bottom: 0; }
    .sys-row-label { color: var(--muted); }
    .sys-row-value { font-weight: 600; color: var(--text); }

    /* tip rows */
    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
        color: var(--muted);
        line-height: 1.5;
    }
    .tip-item:last-child { border-bottom: none; padding-bottom: 0; }
    .tip-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        margin-top: 0.05rem;
    }

    /* ── Divider ─────────────────────────────────────────────── */
    .section-heading {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--muted);
        margin-bottom: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-heading::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $jakartaNow = now()->setTimezone('Asia/Jakarta');
    $greet = match(true) {
        $jakartaNow->hour < 11  => 'Selamat Pagi',
        $jakartaNow->hour < 15  => 'Selamat Siang',
        $jakartaNow->hour < 18  => 'Selamat Sore',
        default                 => 'Selamat Malam',
    };
@endphp

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-badge">
        <i class="bi bi-shield-fill-check"></i> Panel Admin
    </div>
    <div class="welcome-name">{{ $greet }}, <span>{{ e($user->name) }}!</span></div>
    <div class="welcome-role">
        <i class="bi bi-person-badge-fill"></i> {{ e($user->role?->name ?? '—') }}
    </div>
    <div class="welcome-date">
        <i class="bi bi-calendar3"></i>
        {{ $jakartaNow->translatedFormat('l, d F Y') }} · {{ $jakartaNow->format('H:i') }} WIB
    </div>
</div>

{{-- Quick Access --}}
<div class="section-heading"><i class="bi bi-grid-fill"></i> Akses Cepat</div>
<div class="quick-grid">
    @php
        $quickLinks = [
            ['label' => 'Dashboard Kasir', 'sub' => 'Kelola pesanan real-time', 'icon' => 'bi-receipt-cutoff', 'color' => 'gold',   'route' => 'cashier.dashboard'],
            ['label' => 'Rekap Pendapatan','sub' => 'Laporan omzet & transaksi','icon' => 'bi-bar-chart-fill','color' => 'green',  'route' => 'admin.reports.index'],
            ['label' => 'Manajemen User', 'sub' => 'Akun & hak akses staf',   'icon' => 'bi-people-fill',    'color' => 'blue',   'route' => 'admin.users.index'],
            ['label' => 'Manajemen Role', 'sub' => 'Klasifikasi role sistem',  'icon' => 'bi-shield-lock-fill','color' => 'purple','route' => 'admin.roles.index'],
            ['label' => 'Menu Makanan',   'sub' => 'Produk & katalog dimsum',  'icon' => 'bi-basket-fill',    'color' => 'red',    'route' => 'admin.menus.index'],
            ['label' => 'Pengaturan',     'sub' => 'Konfigurasi sistem',       'icon' => 'bi-gear-fill',      'color' => 'indigo', 'route' => 'admin.settings.index'],
        ];
    @endphp
    @foreach($quickLinks as $link)
        @php
            try { $url = route($link['route']); } catch (\Exception $e) { $url = '#'; }
        @endphp
        <a href="{{ $url }}" class="quick-card {{ $link['color'] }}">
            <div class="quick-icon">
                <i class="bi {{ $link['icon'] }}"></i>
            </div>
            <div>
                <div class="quick-label">{{ $link['label'] }}</div>
                <div class="quick-sub">{{ $link['sub'] }}</div>
            </div>
            <div class="quick-arrow">
                Buka <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    @endforeach
</div>

{{-- Info Row --}}
<div class="section-heading"><i class="bi bi-info-circle-fill"></i> Informasi Sistem</div>
<div class="info-grid">
    {{-- System Info --}}
    <div class="info-card">
        <div class="info-card-title"><i class="bi bi-server"></i> Status Akun</div>
        <div class="sys-row">
            <span class="sys-row-label">Username</span>
            <span class="sys-row-value" style="font-family: monospace; color: var(--gold);">{{ e($user->username ?? $user->name) }}</span>
        </div>
        <div class="sys-row">
            <span class="sys-row-label">Role</span>
            <span class="sys-row-value">{{ e($user->role?->name ?? '—') }}</span>
        </div>
        <div class="sys-row">
            <span class="sys-row-label">Email</span>
            <span class="sys-row-value" style="font-size:0.82rem;">{{ e($user->email ?? '—') }}</span>
        </div>
        <div class="sys-row">
            <span class="sys-row-label">Akses Berakhir</span>
            <span class="sys-row-value" style="{{ $user->expired_at ? 'color: var(--warning);' : 'color: var(--success);' }}">
                @if($user->expired_at)
                    {{ $user->expired_at->format('d/m/Y') }}
                @else
                    <i class="bi bi-infinity"></i> Selamanya
                @endif
            </span>
        </div>
        <div class="sys-row">
            <span class="sys-row-label">Login Terakhir</span>
            <span class="sys-row-value" style="font-size:0.82rem; color: var(--muted);">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    {{-- Tips --}}
    <div class="info-card">
        <div class="info-card-title"><i class="bi bi-lightbulb-fill"></i> Panduan Cepat</div>
        <div class="tip-item">
            <div class="tip-icon" style="background: rgba(245,158,11,0.1); color: var(--gold);"><i class="bi bi-receipt-cutoff"></i></div>
            <span>Gunakan <strong style="color:var(--text);">Dashboard Kasir</strong> untuk melihat dan memproses pesanan masuk secara real-time.</span>
        </div>
        <div class="tip-item">
            <div class="tip-icon" style="background: rgba(52,211,153,0.1); color: var(--success);"><i class="bi bi-bar-chart-fill"></i></div>
            <span>Cek <strong style="color:var(--text);">Rekap Pendapatan</strong> untuk melihat laporan omzet harian & bulanan.</span>
        </div>
        <div class="tip-item">
            <div class="tip-icon" style="background: rgba(96,165,250,0.1); color: var(--info);"><i class="bi bi-shield-lock-fill"></i></div>
            <span>Atur <strong style="color:var(--text);">Role & Role Menu</strong> untuk mengontrol hak akses setiap staf.</span>
        </div>
        <div class="tip-item">
            <div class="tip-icon" style="background: rgba(167,139,250,0.1); color: #a78bfa;"><i class="bi bi-key-fill"></i></div>
            <span>Klik nama akun di bawah sidebar untuk <strong style="color:var(--text);">ganti password</strong> kapan saja.</span>
        </div>
    </div>
</div>
@endsection
