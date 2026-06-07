@extends('layouts.admin')

@section('title', 'Pesan di Kasir – Kumaw Dimsum')

@section('styles')
<style>
    /* ================================================================
       POS LAYOUT — Two-panel grid
    ================================================================ */
    .pos-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── Category Tabs ────────────────────────────────────────────── */
    .cat-tabs {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        flex-wrap: nowrap;
        margin-bottom: 1.25rem;
        padding-bottom: 0.25rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .cat-tabs::-webkit-scrollbar { display: none; }

    .cat-tab {
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        transition: all 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
        -webkit-tap-highlight-color: transparent;
    }

    .cat-tab:hover   { color: var(--text); background: var(--surface-hover); }
    .cat-tab.active  { background: var(--gold-dim); border-color: var(--gold-border); color: var(--gold); }

    /* ── Menu Grid ────────────────────────────────────────────────── */
    .menu-section { display: none; }
    .menu-section.active { display: block; }

    .menu-section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--muted);
        margin-bottom: 0.875rem;
    }

    .menu-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .menu-card {
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 0.875rem;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s, transform 0.15s;
        position: relative;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }

    .menu-card:hover {
        border-color: rgba(245,158,11,0.3);
        background: var(--surface-hover);
        transform: translateY(-2px);
    }

    .menu-card:active { transform: scale(0.97); }

    .menu-card.in-cart {
        border-color: var(--gold);
        background: var(--gold-dim);
    }

    .menu-card-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .menu-card-price {
        font-size: 0.8125rem;
        color: var(--gold);
        font-weight: 700;
    }

    .menu-card-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--gold);
        color: #1a0a00;
        font-size: 0.7rem;
        font-weight: 800;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .menu-card.in-cart .menu-card-badge { display: flex; }

    /* ── Order Panel (right side) ─────────────────────────────────── */
    .order-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        position: sticky;
        top: calc(var(--topbar-h) + 1rem);
    }

    .order-panel-header {
        padding: 1.125rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .order-panel-title {
        font-size: 0.9375rem;
        font-weight: 700;
    }

    .order-panel-body {
        padding: 1.125rem 1.25rem;
    }

    /* ── Cart Items ───────────────────────────────────────────────── */
    .cart-items {
        min-height: 60px;
        margin-bottom: 1rem;
    }

    .cart-empty {
        text-align: center;
        padding: 1.5rem 1rem;
        color: var(--muted);
        font-size: 0.875rem;
    }

    .cart-item {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--border);
    }

    .cart-item:last-child { border-bottom: none; }

    .cart-item-info { flex: 1; min-width: 0; }

    .cart-item-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cart-item-price {
        font-size: 0.75rem;
        color: var(--muted);
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface-hover);
        color: var(--text);
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
        flex-shrink: 0;
    }

    .qty-btn:hover { background: rgba(245,158,11,0.15); border-color: var(--gold-border); }
    .qty-btn.remove { color: var(--error); border-color: rgba(248,113,113,0.3); }
    .qty-btn.remove:hover { background: rgba(248,113,113,0.1); }

    .qty-display {
        font-size: 0.875rem;
        font-weight: 700;
        min-width: 22px;
        text-align: center;
    }

    /* ── Order Summary ────────────────────────────────────────────── */
    .order-summary {
        border-top: 1px solid var(--border);
        padding-top: 0.875rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.8125rem;
        color: var(--muted);
        padding: 0.2rem 0;
    }

    .summary-row.total {
        font-size: 1rem;
        font-weight: 800;
        color: var(--gold);
        padding-top: 0.5rem;
        margin-top: 0.25rem;
        border-top: 1px dashed var(--border);
    }

    /* ── Customer Info Form ───────────────────────────────────────── */
    .customer-form {
        margin-bottom: 1rem;
    }

    .customer-form .form-group { margin-bottom: 0.75rem; }

    .customer-form label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 0.3rem;
    }

    .customer-form input,
    .customer-form select {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        width: 100%;
    }

    .type-btns {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.375rem;
    }

    .type-btn {
        padding: 0.45rem 0;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        cursor: pointer;
        text-align: center;
        transition: all 0.15s;
    }

    .type-btn:hover  { background: var(--surface-hover); color: var(--text); }
    .type-btn.active { background: var(--gold-dim); border-color: var(--gold-border); color: var(--gold); }

    .pay-btns {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.375rem;
    }

    /* Hidden inputs for form submission */
    #cart-hidden-inputs { display: none; }

    /* ── Submit button ────────────────────────────────────────────── */
    .btn-submit-order {
        width: 100%;
        padding: 0.875rem;
        font-size: 1rem;
        font-weight: 700;
        border-radius: var(--radius);
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-submit-order:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        transform: none;
    }

    /* Pulse animation on cart badge */
    @keyframes cartPop {
        0%   { transform: scale(1); }
        40%  { transform: scale(1.35); }
        100% { transform: scale(1); }
    }

    .pop { animation: cartPop 0.25s ease; }

    /* Removed mobile fab and sheet CSS */
</style>
<style>
    @@media (max-width: 1100px) {
        .pos-grid { grid-template-columns: 1fr; display: flex; flex-direction: column; }
        .menu-panel { order: 1; }
        .order-panel { order: 2; position: static; width: 100%; margin-bottom: 2rem; }
    }
    @@media (max-width: 768px) {
        .menu-cards { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.625rem; }
        .menu-card { padding: 0.75rem; }
        .menu-panel { padding-bottom: 2rem; }
        .page-header h1 { font-size: 1.2rem; }
        .page-header p  { font-size: 0.8rem; }
    }
    @@media (max-width: 480px) {
        .menu-cards { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .cat-tab { padding: 0.3rem 0.625rem; font-size: 0.75rem; }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>🛒 Pesan di Kasir</h1>
        <p>Buat pesanan langsung untuk pelanggan di kasir</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong>Pesanan gagal dibuat:</strong>
            <ul style="margin-top:0.3rem;padding-left:1.2rem">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif



<form id="pos-form" method="POST" action="{{ route('cashier.order.store') }}">
    @csrf

    {{-- Hidden inputs generated dynamically by JS --}}
    <div id="cart-hidden-inputs"></div>

    <div class="pos-grid">

        {{-- ============================================================
             LEFT: Menu browser
        ============================================================ --}}
        <div class="menu-panel">

            {{-- Category tabs --}}
            <div class="cat-tabs" id="cat-tabs">
                <button type="button" class="cat-tab active" data-cat="all" onclick="switchCat('all', this)">
                    🍽️ Semua
                </button>
                @foreach($menus->keys() as $cat)
                    <button type="button"
                            class="cat-tab"
                            data-cat="{{ $cat }}"
                            onclick="switchCat('{{ $cat }}', this)">
                        {{ ucfirst($cat) }}
                    </button>
                @endforeach
            </div>

            {{-- "All" section --}}
            <div class="menu-section active" id="section-all">
                @foreach($menus as $cat => $items)
                    <div class="menu-section-title">
                        {{ ucfirst($cat) }}
                    </div>
                    <div class="menu-cards">
                        @foreach($items as $menu)
                            <div class="menu-card"
                                 id="card-{{ $menu->id }}"
                                 onclick="addToCart({{ $menu->id }}, {{ json_encode($menu->name) }}, {{ $menu->price }})"
                                 title="{{ $menu->name }} — Rp {{ number_format($menu->price, 0, ',', '.') }}">
                                <span class="menu-card-badge" id="badge-{{ $menu->id }}">0</span>
                                <div class="menu-card-name">{{ $menu->name }}</div>
                                <div class="menu-card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Per-category sections --}}
            @foreach($menus as $cat => $items)
                <div class="menu-section" id="section-{{ $cat }}">
                    <div class="menu-section-title">{{ ucfirst($cat) }}</div>
                    <div class="menu-cards">
                        @foreach($items as $menu)
                            <div class="menu-card"
                                 id="card-cat-{{ $cat }}-{{ $menu->id }}"
                                 onclick="addToCart({{ $menu->id }}, {{ json_encode($menu->name) }}, {{ $menu->price }})"
                                 title="{{ $menu->name }}">
                                <span class="menu-card-badge" id="badge-cat-{{ $cat }}-{{ $menu->id }}">0</span>
                                <div class="menu-card-name">{{ $menu->name }}</div>
                                <div class="menu-card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>{{-- /menu-panel --}}

        {{-- ============================================================
             RIGHT: Order panel
        ============================================================ --}}
        <div class="order-panel">
            <div class="order-panel-header">
                <span class="order-panel-title">🧾 Pesanan</span>
                <button type="button"
                        id="btn-clear-cart"
                        class="btn btn-danger btn-sm"
                        onclick="clearCart()"
                        style="display:none">
                    🗑️ Hapus Semua
                </button>
            </div>

            <div class="order-panel-body">

                {{-- Cart items --}}
                <div class="cart-items" id="cart-items">
                    <div class="cart-empty" id="cart-empty">
                        <div style="font-size:1.75rem;margin-bottom:0.375rem">🛒</div>
                        Klik menu untuk menambah pesanan
                    </div>
                </div>

                {{-- Order summary --}}
                <div class="order-summary" id="order-summary" style="display:none">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="sum-subtotal">Rp 0</span>
                    </div>

                    <div class="summary-row total">
                        <span>TOTAL</span>
                        <span id="sum-total">Rp 0</span>
                    </div>
                </div>

                {{-- Customer info --}}
                <div class="customer-form">

                    <div class="form-group">
                        <label for="customer_name">Nama Pelanggan *</label>
                        <input type="text"
                               id="customer_name"
                               name="customer_name"
                               placeholder="Nama pelanggan"
                               value="{{ old('customer_name') }}"
                               required
                               maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="phone_number">No. Telepon</label>
                        <input type="tel"
                               id="phone_number"
                               name="phone_number"
                               placeholder="08xxxxxxxxxx"
                               value="{{ old('phone_number') }}"
                               maxlength="20">
                    </div>

                    <div class="form-group">
                        <label>Jenis Pesanan *</label>
                        <div class="type-btns" id="type-btns">
                            @foreach(['dine_in' => '🍽️ Makan', 'takeaway' => '📦 Bawa'] as $val => $label)
                                <button type="button"
                                        class="type-btn {{ old('type', 'dine_in') === $val ? 'active' : '' }}"
                                        data-value="{{ $val }}"
                                        onclick="selectType('{{ $val }}', this)">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="type-input" name="type" value="{{ old('type', 'dine_in') }}">
                    </div>

                    <div class="form-group" id="table-row" style="{{ old('type', 'dine_in') === 'dine_in' ? '' : 'display:none' }}">
                        <label for="table_number">Nomor Meja</label>
                        <input type="number"
                               id="table_number"
                               name="table_number"
                               placeholder="Contoh: 5"
                               value="{{ old('table_number') }}"
                               min="1"
                               max="999">
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran *</label>
                        <div class="pay-btns" id="pay-btns">
                            @foreach(['cash' => '💵 Tunai', 'qris' => '📱 QRIS'] as $val => $label)
                                <button type="button"
                                        class="type-btn {{ old('payment_method', 'cash') === $val ? 'active' : '' }}"
                                        data-value="{{ $val }}"
                                        onclick="selectPay('{{ $val }}', this)">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="payment-input" name="payment_method" value="{{ old('payment_method', 'cash') }}">
                    </div>

                    <div class="form-group">
                        <label for="customer_notes">Catatan (opsional)</label>
                        <input type="text"
                               id="customer_notes"
                               name="customer_notes"
                               placeholder="Misal: tidak pedas, no MSG…"
                               value="{{ old('customer_notes') }}"
                               maxlength="1000">
                    </div>

                </div>

                {{-- Submit --}}
                <button type="submit"
                        id="btn-submit"
                        class="btn btn-gold btn-submit-order"
                        disabled>
                    Buat Pesanan
                </button>

            </div>{{-- /order-panel-body --}}
        </div>{{-- /order-panel --}}

    </div>{{-- /pos-grid --}}
</form>
@endsection

@section('scripts')
<script>
    /* ================================================================
       CART STATE
    ================================================================ */
    // cart = { menuId: { id, name, price, qty } }
    const cart = {};

    const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

    /* ---- Add / increment item ---- */
    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { id, name, price, qty: 1 };
        }
        renderCart();
        updateCardBadge(id);
        popBadge(id);
    }

    /* ---- Change qty ---- */
    function changeQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        renderCart();
        updateCardBadge(id);
    }

    /* ---- Clear cart ---- */
    function clearCart() {
        Object.keys(cart).forEach(id => {
            delete cart[id];
            updateCardBadge(id);
        });
        renderCart();
    }

    /* ---- Update the qty badge on a menu card ---- */
    function updateCardBadge(id) {
        const qty = cart[id]?.qty ?? 0;
        // Update all cards with this menu id (they appear in multiple category sections)
        document.querySelectorAll(`[id^="badge-"][id$="-${id}"], #badge-${id}`).forEach(el => {
            el.textContent = qty;
        });
        document.querySelectorAll(`[id^="card-"][id$="-${id}"], #card-${id}`).forEach(el => {
            el.classList.toggle('in-cart', qty > 0);
        });
    }

    /* ---- Pop animation on badge ---- */
    function popBadge(id) {
        document.querySelectorAll(`[id^="badge-"][id$="-${id}"], #badge-${id}`).forEach(el => {
            el.classList.remove('pop');
            void el.offsetWidth;
            el.classList.add('pop');
        });
    }



    /* ---- Render cart list & totals ---- */
    function renderCart() {
        const items = Object.values(cart);
        const isEmpty = items.length === 0;

        document.getElementById('cart-empty').style.display  = isEmpty ? 'block' : 'none';
        document.getElementById('order-summary').style.display = isEmpty ? 'none' : 'block';
        document.getElementById('btn-clear-cart').style.display = isEmpty ? 'none' : '';
        document.getElementById('btn-submit').disabled = isEmpty;

        // Build cart rows
        const container = document.getElementById('cart-items');
        container.querySelectorAll('.cart-item').forEach(el => el.remove());

        let subtotal = 0;

        items.forEach(item => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;

            const html = `
                <div class="cart-item-info">
                    <div class="cart-item-name">${escHtml(item.name)}</div>
                    <div class="cart-item-price">${formatRp(item.price)} × ${item.qty} = ${formatRp(itemTotal)}</div>
                </div>
                <div class="cart-item-controls">
                    <button type="button" class="qty-btn remove" onclick="changeQty(${item.id}, -1)" title="Kurangi">−</button>
                    <span class="qty-display">${item.qty}</span>
                    <button type="button" class="qty-btn" onclick="changeQty(${item.id}, 1)" title="Tambah">+</button>
                </div>
            `;

            const div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = html;
            container.appendChild(div);
        });

        // Totals
        const total = subtotal;

        document.getElementById('sum-subtotal').textContent = formatRp(subtotal);
        document.getElementById('sum-total').textContent    = formatRp(total);

        // Rebuild hidden inputs for form submission
        const hiddenContainer = document.getElementById('cart-hidden-inputs');
        hiddenContainer.innerHTML = '';
        items.forEach((item, idx) => {
            hiddenContainer.innerHTML += `
                <input type="hidden" name="items[${idx}][menu_id]"  value="${item.id}">
                <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
            `;
        });
    }

    /* ---- HTML-escape helper ---- */
    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    /* ================================================================
       CATEGORY SWITCHER
    ================================================================ */
    function switchCat(cat, btn) {
        document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.menu-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + cat).classList.add('active');
    }

    /* ================================================================
       TYPE & PAYMENT SELECTORS
    ================================================================ */
    function selectType(val, btn) {
        document.querySelectorAll('#type-btns .type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('type-input').value = val;

        // Show/hide table number field
        document.getElementById('table-row').style.display = (val === 'dine_in') ? '' : 'none';
    }

    function selectPay(val, btn) {
        document.querySelectorAll('#pay-btns .type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('payment-input').value = val;
    }

    /* ================================================================
       FORM SUBMIT GUARD — prevent double-submit
    ================================================================ */
    document.getElementById('pos-form').addEventListener('submit', function (e) {
        const items = Object.values(cart);
        if (items.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu menu terlebih dahulu.');
            return;
        }

        const submitBtn = document.getElementById('btn-submit');
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Memproses…';
    });
</script>
@endsection
