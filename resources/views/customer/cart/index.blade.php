@extends('layouts.app')

@section('title', 'Keranjang – Kumaw Dimsum')

@section('styles')
<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ---- Cart item ---- */
    .cart-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.125rem 0;
        border-bottom: 1px solid var(--border);
    }
    .cart-item:last-child { border-bottom: none; }

    .item-emoji {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(99,102,241,0.1));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .item-info { flex: 1; min-width: 0; }
    .item-name { font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.2rem; }
    .item-category { font-size: 0.8125rem; color: var(--muted); }
    .item-notes { font-size: 0.8rem; color: var(--muted); margin-top: 0.25rem; font-style: italic; }
    .item-price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.625rem;
    }
    .item-unit-price { font-size: 0.875rem; color: var(--muted); }
    .item-subtotal { font-size: 0.9375rem; font-weight: 700; color: var(--gold); }

    .item-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .qty-display {
        font-size: 0.9rem;
        font-weight: 600;
        min-width: 32px;
        text-align: center;
        padding: 0.375rem 0.5rem;
        background: var(--bg2);
        border-radius: 6px;
    }

    .btn-remove {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 8px;
        background: rgba(248,113,113,0.1);
        border: 1px solid rgba(248,113,113,0.2);
        color: var(--error);
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 0.2s;
        font-family: inherit;
    }
    .btn-remove:hover { background: rgba(248,113,113,0.2); }

    /* ---- Summary ---- */
    .summary-card {
        position: sticky;
        top: 80px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        padding: 0.375rem 0;
        color: var(--muted);
    }
    .summary-row.total {
        border-top: 1px solid var(--border);
        margin-top: 0.75rem;
        padding-top: 0.875rem;
        color: var(--text);
        font-size: 1rem;
        font-weight: 700;
    }
    .summary-row.total span:last-child { color: var(--gold); }

    .order-form { margin-top: 1.25rem; }

    .type-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.375rem;
        margin-bottom: 1rem;
    }
    .type-tab {
        padding: 0.5rem 0.25rem;
        text-align: center;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--muted);
        cursor: pointer;
        transition: all 0.2s;
    }
    input[type="radio"]:checked + .type-tab {
        background: var(--gold-dim);
        border-color: rgba(245,158,11,0.45);
        color: var(--gold);
    }
    .radio-hidden { display: none; }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
        .summary-card { position: static; }
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🛒 Keranjang Belanja</h1>
            <p>Periksa pesanan kamu sebelum checkout</p>
        </div>
        @if(!empty($cart))
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" id="btn-clear-cart"
                        onclick="return confirm('Kosongkan seluruh keranjang?')">
                    🗑️ Kosongkan
                </button>
            </form>
        @endif
    </div>

    @if(empty($cart))
        <div class="empty-state">
            <div class="icon">🛒</div>
            <h3>Keranjang masih kosong</h3>
            <p>Tambahkan dimsum favoritmu dari halaman menu.</p>
            <a href="{{ route('menu.index') }}" class="btn btn-gold">Lihat Menu</a>
        </div>
    @else
        <div class="cart-layout">
            {{-- Cart items --}}
            <div class="card">
                <div class="section-title">🥟 Item Pesanan ({{ count($cart) }} item)</div>

                @foreach($cart as $key => $item)
                    <div class="cart-item">
                        <div class="item-emoji">
                            {{ match($item['menu_category'] ?? '') {
                                'siomay'  => '🥟',
                                'hakau'   => '🦐',
                                'lumpia'  => '🌯',
                                'bao'     => '🫓',
                                'shumai'  => '🍢',
                                'minuman' => '🍵',
                                default   => '✨',
                            } }}
                        </div>

                        <div class="item-info">
                            <div class="item-name">{{ $item['menu_name'] }}</div>
                            <div class="item-category">{{ ucfirst($item['menu_category'] ?? '') }}</div>
                            @if(!empty($item['notes']))
                                <div class="item-notes">📝 {{ $item['notes'] }}</div>
                            @endif

                            <div class="item-price-row">
                                <div class="item-unit-price">
                                    Rp {{ number_format($item['unit_price'], 0, ',', '.') }} × {{ $item['quantity'] }}
                                </div>
                                <div class="item-subtotal">
                                    Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="item-actions">
                            <span class="qty-display">{{ $item['quantity'] }}</span>
                            <form method="POST" action="{{ route('cart.remove', $item['menu_id']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove" title="Hapus item" id="remove-{{ $item['menu_id'] }}">✕</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Order summary & checkout --}}
            <div class="summary-card">
                <div class="section-title">💰 Ringkasan Pembayaran</div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Pajak (11%)</span>
                    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>

                <form method="POST" action="{{ route('orders.store') }}" class="order-form" id="checkout-form">
                    @csrf

                    <div class="form-group">
                        <label for="customer_name">Nama Pemesan <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            value="{{ old('customer_name') }}"
                            placeholder="Contoh: Budi"
                            required
                        >
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            value="{{ old('phone_number') }}"
                            placeholder="Contoh: 081234567890"
                            required
                        >
                        <div class="form-hint">Digunakan untuk mengirim notifikasi saat pesanan siap diambil.</div>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Jenis Pesanan</label>
                        <div class="type-tabs">
                            @foreach([
                                'dine_in'  => ['label' => '🍽️ Makan', 'id' => 'type-dine'],
                                'takeaway' => ['label' => '📦 Bawa', 'id' => 'type-take'],
                                'delivery' => ['label' => '🛵 Antar', 'id' => 'type-delivery'],
                            ] as $value => $opt)
                                <label for="{{ $opt['id'] }}" style="margin:0;display:contents">
                                    <input
                                        type="radio"
                                        id="{{ $opt['id'] }}"
                                        name="type"
                                        value="{{ $value }}"
                                        class="radio-hidden"
                                        {{ old('type', 'dine_in') === $value ? 'checked' : '' }}
                                    >
                                    <span class="type-tab">{{ $opt['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="table-number-group">
                        <label for="table_number">Nomor Meja (opsional)</label>
                        <input
                            type="number"
                            id="table_number"
                            name="table_number"
                            value="{{ old('table_number') }}"
                            min="1" max="999"
                            placeholder="Contoh: 5"
                        >
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="customer_notes">Catatan (opsional)</label>
                        <textarea
                            id="customer_notes"
                            name="customer_notes"
                            rows="2"
                            maxlength="1000"
                            placeholder="Misalnya: tidak pakai pedas"
                            style="resize:vertical"
                        >{{ old('customer_notes') }}</textarea>
                        @error('customer_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran <span class="text-danger">*</span></label>
                        <div class="type-tabs" style="grid-template-columns: 1fr 1fr;">
                            <label for="payment-cash" style="margin:0;display:contents">
                                <input
                                    type="radio"
                                    id="payment-cash"
                                    name="payment_method"
                                    value="cash"
                                    class="radio-hidden"
                                    {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                                >
                                <span class="type-tab">💵 Bayar di Kasir</span>
                            </label>
                            <label for="payment-qris" style="margin:0;display:contents">
                                <input
                                    type="radio"
                                    id="payment-qris"
                                    name="payment_method"
                                    value="qris"
                                    class="radio-hidden"
                                    {{ old('payment_method') === 'qris' ? 'checked' : '' }}
                                >
                                <span class="type-tab">📱 QRIS</span>
                            </label>
                        </div>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-gold btn-block btn-lg" id="btn-checkout">
                        ✅ Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- QRIS Modal -->
        <div id="qrisModal" class="modal" tabindex="-1" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1050; align-items: center; justify-content: center;">
            <div class="modal-dialog" style="background: var(--surface); border: 1px solid var(--gold); border-radius: var(--radius-lg); padding: 1.5rem; max-width: 400px; width: 90%; text-align: center;">
                <h4 style="color: var(--gold); margin-bottom: 1rem;">Scan QRIS untuk Bayar</h4>
                
                @if(isset($qrisImage) && $qrisImage)
                    <img src="{{ Storage::url($qrisImage) }}" alt="QRIS" style="width: 100%; max-height: 350px; object-fit: contain; border-radius: 8px; margin-bottom: 1rem;">
                @else
                    <div style="padding: 2rem; background: var(--bg2); border-radius: 8px; margin-bottom: 1rem; color: var(--muted);">
                        QRIS belum tersedia. Silakan bayar di kasir.
                    </div>
                @endif
                
                <p style="font-size: 0.9rem; color: var(--muted); margin-bottom: 1.5rem;">Total Tagihan: <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></p>
                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeQrisModal()" style="flex: 1;">Batal</button>
                    <button type="button" class="btn btn-gold" onclick="submitForm()" style="flex: 1;">Saya Sudah Bayar</button>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Show/hide table number based on order type
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const tableGroup = document.getElementById('table-number-group');

    function toggleTableField() {
        const selected = document.querySelector('input[name="type"]:checked')?.value;
        tableGroup.style.display = (selected === 'dine_in') ? 'block' : 'none';
    }

    typeInputs.forEach(i => i.addEventListener('change', toggleTableField));
    toggleTableField(); // initial

    // QRIS logic
    const btnCheckout = document.getElementById('btn-checkout');
    const checkoutForm = document.getElementById('checkout-form');
    const qrisModal = document.getElementById('qrisModal');

    btnCheckout.addEventListener('click', function() {
        // Run native form validation (required fields like Name)
        if (!checkoutForm.reportValidity()) {
            return;
        }

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        if (paymentMethod === 'qris') {
            qrisModal.style.display = 'flex';
        } else {
            checkoutForm.submit();
        }
    });

    function closeQrisModal() {
        qrisModal.style.display = 'none';
    }

    function submitForm() {
        checkoutForm.submit();
    }
</script>
@endsection
