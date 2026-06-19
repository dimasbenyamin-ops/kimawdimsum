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
        position: relative;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.3);
        border-color: rgba(245,158,11,0.25);
    }

    /* ---- Ribbon Badge ---- */
    .ribbon-badge {
        position: absolute;
        top: 12px;
        right: -8px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 4px 0 0 4px;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ribbon-badge::after {
        content: '';
        position: absolute;
        top: 100%;
        right: 0;
        border-top: 6px solid #991b1b;
        border-right: 8px solid transparent;
    }
    .ribbon-badge.gold {
        background: linear-gradient(135deg, var(--gold), #d97706);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }
    .ribbon-badge.gold::after {
        border-top-color: #b45309;
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

    /* ---- Add to cart form & Stepper ---- */
    .add-form {
        display: flex;
        gap: 0.5rem;
    }
    .qty-stepper {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
    }
    .qty-stepper button {
        background: transparent;
        border: none;
        color: var(--gold);
        font-size: 1.2rem;
        width: 32px;
        height: 100%;
        cursor: pointer;
        transition: background 0.2s;
    }
    .qty-stepper button:hover { background: rgba(245,158,11,0.1); }
    .qty-stepper input {
        width: 36px;
        text-align: center;
        background: transparent;
        border: none;
        color: var(--text);
        font-size: 1rem;
        font-weight: bold;
        -moz-appearance: textfield;
        padding: 0;
        margin: 0;
    }
    .qty-stepper input::-webkit-outer-spin-button,
    .qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-input:focus { outline: none; }

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

    /* ---- Floating Cart ---- */
    .floating-cart {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, var(--gold), #d97706);
        color: #1a0a00;
        padding: 0.75rem 1.25rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 10px 25px rgba(245,158,11,0.4);
        text-decoration: none;
        z-index: 50;
        transition: transform 0.2s, box-shadow 0.2s;
        width: max-content;
        max-width: 90vw;
    }
    .floating-cart:hover {
        transform: translateX(-50%) translateY(-2px);
        box-shadow: 0 15px 30px rgba(245,158,11,0.5);
    }
    .fc-icon { font-size: 1.5rem; }
    .fc-info { flex: 1; }
    .fc-title { font-weight: 700; font-size: 0.9rem; }
    .fc-count { font-size: 0.8rem; font-weight: 500; opacity: 0.9; }
    .fc-action { font-weight: 700; font-size: 0.9rem; background: rgba(0,0,0,0.1); padding: 0.25rem 0.75rem; border-radius: 999px; }

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
        <a href="{{ route('menu.index', ['category' => 'all']) }}"
           class="cat-btn {{ $activeCategory === 'all' ? 'active' : '' }}"
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
        @if($activeCategory === 'all')
            <div class="section-heading">
                <h2>{{ $categoryLabels[$category] ?? ucfirst($category) }}</h2>
            </div>
        @endif

        <div class="menu-grid">
            @foreach($items as $menu)
                <div class="menu-card">
                    @if($menu->badge)
                        @php
                            $isBestSeller = str_contains(strtolower($menu->badge), 'best') || str_contains(strtolower($menu->badge), 'rekomendasi');
                            $badgeClass = $isBestSeller ? 'gold' : '';
                        @endphp
                        <div class="ribbon-badge {{ $badgeClass }}">{{ $menu->badge }}</div>
                    @endif

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

                    <div class="menu-card-footer" id="footer-{{ $menu->id }}">
                        @if(!Auth::check())
                            @php
                                $inCartQty = isset($cart[$menu->id]) ? $cart[$menu->id]['quantity'] : 0;
                            @endphp
                            
                            <!-- Add Button -->
                            <div class="btn-group-add" style="{{ $inCartQty > 0 ? 'display:none;' : '' }}">
                                <button type="button" class="btn-add btn-cart-add" data-id="{{ $menu->id }}" data-url="{{ route('cart.add') }}" style="width:100%">
                                    <i class="bi bi-cart-plus"></i> Tambah
                                </button>
                            </div>

                            <!-- Stepper inside Cart -->
                            <div class="qty-stepper cart-stepper" style="width:100%; justify-content:space-between; padding: 0.15rem; border-color: var(--gold); {{ $inCartQty == 0 ? 'display:none;' : '' }}">
                                <button type="button" class="btn-cart-minus" data-id="{{ $menu->id }}" data-url-update="{{ route('cart.update', $menu->id) }}" data-url-remove="{{ route('cart.remove', $menu->id) }}" style="width:40px; font-size:1.5rem;">-</button>
                                <span class="cart-qty-display" id="qty-{{ $menu->id }}" style="font-weight:bold; color:var(--text); font-size:1.1rem; width:40px; text-align:center; display:inline-block;">{{ $inCartQty }}</span>
                                <button type="button" class="btn-cart-plus" data-id="{{ $menu->id }}" data-url-update="{{ route('cart.update', $menu->id) }}" style="width:40px; font-size:1.2rem;">+</button>
                            </div>
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

    @if(!Auth::check())
        <a href="{{ route('cart.index') }}" class="floating-cart" id="floating-cart" style="display: {{ isset($cartCount) && $cartCount > 0 ? 'flex' : 'none' }};">
            <div class="fc-icon">🛒</div>
            <div class="fc-info">
                <div class="fc-title">Keranjang Belanja</div>
                <div class="fc-count"><span class="cart-badge-bottom">{{ $cartCount ?? 0 }}</span> Macam Menu</div>
            </div>
            <div class="fc-action">Lanjut ➔</div>
        </a>
    @endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    // Add to cart directly
    document.querySelectorAll('.btn-cart-add').forEach(btn => {
        btn.addEventListener('click', async () => {
            const menuId = btn.dataset.id;
            const url = btn.dataset.url;
            
            btn.innerHTML = '⏳...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('menu_id', menuId);
            formData.append('quantity', 1);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    btn.closest('.btn-group-add').style.display = 'none';
                    const footer = document.getElementById('footer-' + menuId);
                    footer.querySelector('.cart-stepper').style.display = 'flex';
                    footer.querySelector('.cart-qty-display').innerText = '1';
                    
                    updateCartBadge(data.cart_count);
                }
            } catch (err) {
                console.error(err);
            }
            btn.innerHTML = '<i class="bi bi-cart-plus"></i> Tambah';
            btn.disabled = false;
        });
    });

    // Stepper Plus
    document.querySelectorAll('.btn-cart-plus').forEach(btn => {
        btn.addEventListener('click', async () => {
            const menuId = btn.dataset.id;
            const url = btn.dataset.urlUpdate;
            const qtyDisplay = document.getElementById('qty-' + menuId);
            let qty = parseInt(qtyDisplay.innerText) + 1;
            if (qty > 99) return;
            
            qtyDisplay.innerText = '⏳';
            await updateCart(url, qty, 'PUT');
            qtyDisplay.innerText = qty;
        });
    });

    // Stepper Minus
    document.querySelectorAll('.btn-cart-minus').forEach(btn => {
        btn.addEventListener('click', async () => {
            const menuId = btn.dataset.id;
            const urlUpdate = btn.dataset.urlUpdate;
            const urlRemove = btn.dataset.urlRemove;
            const qtyDisplay = document.getElementById('qty-' + menuId);
            let qty = parseInt(qtyDisplay.innerText) - 1;
            
            qtyDisplay.innerText = '⏳';
            
            if (qty > 0) {
                await updateCart(urlUpdate, qty, 'PUT');
                qtyDisplay.innerText = qty;
            } else {
                const res = await updateCart(urlRemove, 0, 'DELETE');
                if(res && res.success) {
                    const footer = document.getElementById('footer-' + menuId);
                    footer.querySelector('.cart-stepper').style.display = 'none';
                    footer.querySelector('.btn-group-add').style.display = 'block';
                }
            }
        });
    });

    async function updateCart(url, qty, method) {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', method);
        if (qty > 0) formData.append('quantity', qty);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                updateCartBadge(data.cart_count);
                return data;
            }
        } catch (err) {
            console.error(err);
        }
        return null;
    }

    function updateCartBadge(count) {
        // Update header badge
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge && count !== undefined) {
            cartBadge.innerText = count;
            cartBadge.style.display = count > 0 ? 'flex' : 'none';
        }
        
        // Update floating bottom cart
        const floatingCart = document.getElementById('floating-cart');
        if (floatingCart && count !== undefined) {
            const bottomBadge = document.querySelector('.cart-badge-bottom');
            if (bottomBadge) bottomBadge.innerText = count;
            floatingCart.style.display = count > 0 ? 'flex' : 'none';
        }
    }
});
</script>
@endsection
