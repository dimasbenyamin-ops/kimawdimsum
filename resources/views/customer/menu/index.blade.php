@extends('layouts.app')

@section('title', 'Menu – Kumaw Dimsum')
@section('meta_description', 'Temukan berbagai pilihan dimsum autentik di Kumaw Dimsum.')

@section('styles')
<style>
    /* ---- Hero ---- */
    .hero {
        text-align: center;
        padding: 3rem 1rem 2rem;
        margin-bottom: 2.5rem;
        position: relative;
    }
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 0%, rgba(245,158,11,0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-badge {
        display: inline-block;
        padding: 0.25rem 0.875rem;
        background: rgba(245,158,11,0.12);
        border: 1px solid rgba(245,158,11,0.25);
        border-radius: 999px;
        color: var(--gold);
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .hero h1 {
        font-size: clamp(1.75rem, 5vw, 2.75rem);
        font-weight: 800;
        line-height: 1.15;
        background: linear-gradient(135deg, var(--text) 30%, var(--gold) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
    }
    .hero p { color: var(--muted); font-size: 1rem; }

    /* ---- Category filter ---- */
    .category-bar {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
        margin-bottom: 2.5rem;
        scrollbar-width: none;
    }
    .category-bar::-webkit-scrollbar { display: none; }
    .cat-btn {
        flex-shrink: 0;
        padding: 0.5rem 1.125rem;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .cat-btn:hover { border-color: rgba(245,158,11,0.4); color: var(--gold); }
    .cat-btn.active {
        background: var(--gold-dim);
        border-color: rgba(245,158,11,0.45);
        color: var(--gold);
    }

    /* ---- Section heading ---- */
    .section-heading {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .section-heading h2 {
        font-size: 1.125rem;
        font-weight: 700;
    }
    .section-heading::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ---- Menu grid ---- */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 3rem;
    }

    /* ---- Menu card ---- */
    .menu-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        display: flex;
        flex-direction: column;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.3);
        border-color: rgba(245,158,11,0.25);
    }

    .menu-card-img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        background: var(--bg2);
    }
    .menu-card-img-placeholder {
        width: 100%;
        aspect-ratio: 4/3;
        background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(99,102,241,0.08));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
    }

    .menu-card-body {
        padding: 1rem 1.125rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }
    .menu-card-name {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text);
        line-height: 1.3;
    }
    .menu-card-desc {
        font-size: 0.8125rem;
        color: var(--muted);
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .menu-card-price {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gold);
        margin-top: auto;
        padding-top: 0.5rem;
    }

    .menu-card-footer {
        padding: 0.75rem 1.125rem 1rem;
    }

    /* ---- Add to cart form ---- */
    .add-form {
        display: flex;
        gap: 0.5rem;
    }
    .qty-input {
        width: 60px;
        padding: 0.5rem 0.625rem;
        text-align: center;
        font-size: 0.9rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-family: inherit;
        outline: none;
    }
    .qty-input:focus { border-color: var(--gold); }

    .btn-add {
        flex: 1;
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, var(--gold), #d97706);
        border: none;
        border-radius: 8px;
        color: #1a0a00;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: opacity 0.2s, transform 0.15s;
    }
    .btn-add:hover { opacity: 0.88; transform: translateY(-1px); }
    .btn-add:active { transform: translateY(0); }

    .btn-login-prompt {
        display: block;
        width: 100%;
        padding: 0.5rem;
        text-align: center;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--muted);
        font-size: 0.8125rem;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-login-prompt:hover { background: var(--surface-hover); color: var(--text); }

    @media (max-width: 640px) {
        .menu-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    }
</style>
@endsection

@section('content')
    {{-- Hero --}}
    <div class="hero">
        <div class="hero-badge">🥟 Authentic Dimsum</div>
        <h1>Pilih Dimsum<br>Favorit Kamu</h1>
        <p>Disajikan segar langsung dari dapur kami</p>
    </div>

    {{-- Category filter --}}
    <div class="category-bar">
        <a href="{{ route('menu.index') }}"
           class="cat-btn {{ $activeCategory === null ? 'active' : '' }}"
           id="cat-all">
            🍽️ Semua
        </a>
        @foreach($categoryLabels as $key => $label)
            <a href="{{ route('menu.index', ['category' => $key]) }}"
               class="cat-btn {{ $activeCategory === $key ? 'active' : '' }}"
               id="cat-{{ $key }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Menu sections --}}
    @forelse($menus as $category => $items)
        @if(!$activeCategory)
            <div class="section-heading">
                <h2>{{ $categoryLabels[$category] ?? ucfirst($category) }}</h2>
            </div>
        @endif

        <div class="menu-grid">
            @foreach($items as $menu)
                <div class="menu-card">
                    {{-- Image --}}
                    @if($menu->image_path)
                        <img 
                            src="{{ Str::startsWith($menu->image_path, 'images/') ? asset($menu->image_path) : asset('storage/' . $menu->image_path) }}" 
                            alt="{{ e($menu->name) }}"
                            class="menu-card-img"
                            loading="lazy"
                        >
                    @else
                        <div class="menu-card-img-placeholder">
                            {{ match($menu->category) {
                                'siomay'  => '🥟',
                                'hakau'   => '🦐',
                                'lumpia'  => '🌯',
                                'bao'     => '🫓',
                                'shumai'  => '🍢',
                                'minuman' => '🍵',
                                default   => '✨',
                            } }}
                        </div>
                    @endif

                    <div class="menu-card-body">
                        <div class="menu-card-name">{{ $menu->name }}</div>
                        @if($menu->description)
                            <div class="menu-card-desc">{{ $menu->description }}</div>
                        @endif
                        <div class="menu-card-price">{{ $menu->formattedPrice }}</div>
                    </div>

                    <div class="menu-card-footer">
                        {{-- Customers are guests now (not authenticated). Hide add-to-cart for staff --}}
                        @if(!Auth::check())
                            <form method="POST" action="{{ route('cart.add') }}" class="add-form">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="99"
                                    class="qty-input"
                                    aria-label="Jumlah"
                                >
                                <button type="submit" class="btn-add" id="add-{{ $menu->id }}">
                                    <i class="bi bi-cart-plus"></i> Tambah
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-login-prompt">
                                🔒 Masuk untuk memesan
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="empty-state">
            <div class="icon">🥟</div>
            <h3>Menu belum tersedia</h3>
            <p>Kategori ini sedang dalam persiapan. Coba kategori lain!</p>
            <a href="{{ route('menu.index') }}" class="btn btn-gold">Lihat Semua Menu</a>
        </div>
    @endforelse
@endsection
