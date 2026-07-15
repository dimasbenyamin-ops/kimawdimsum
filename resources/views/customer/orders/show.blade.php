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

        @if($order->estimated_ready_at && in_array($order->status, ['confirmed', 'preparing']))
            <div class="alert alert-info" style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:1.5rem">⏳</span>
                <div>
                    <div style="font-weight:600; font-size:1rem;">Estimasi Waktu Tunggu</div>
                    <div style="font-size:0.875rem; color:var(--muted)">Pesananmu sedang disiapkan. Estimasi siap: <strong>{{ $order->estimated_ready_at->diffForHumans() }}</strong> ({{ $order->estimated_ready_at->format('H:i') }})</div>
                </div>
            </div>
        @endif
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
                <div style="margin-top: 1.5rem;">
                    <button class="btn btn-secondary btn-block" onclick="printInvoice()" style="padding: 0.875rem; font-size: 1.05rem; background-color: var(--surface); color: var(--text); border: 1px solid var(--border);">
                        🖨️ Struk
                    </button>
                </div>
            @elseif($order->status === 'pending_payment' && $order->payment_method === 'qris')
                <div style="margin-top: 1.5rem;">
                    <button id="pay-button" class="btn btn-gold btn-block" style="padding: 0.875rem; font-size: 1.05rem;">
                        💳 Bayar Non-Tunai
                    </button>
                </div>
            @elseif($order->status === 'pending_payment' && $order->payment_method === 'cash')
                <div class="alert alert-info" style="margin-top: 1.5rem; font-size: 0.85rem; padding: 0.75rem;">
                    Silakan lakukan pembayaran di kasir.
                </div>
            @endif
        </div>
    </div>

    {{-- Review Section --}}
    @if($order->isCompleted() && !$order->review()->exists())
        <div class="card" style="margin-top: 2rem; background: linear-gradient(to bottom right, var(--surface), rgba(245,158,11,0.05)); border: 1px solid rgba(245,158,11,0.3);">
            <div style="text-align:center; margin-bottom: 1rem;">
                <h3 style="color:var(--gold); font-size:1.25rem;">🌟 Bagaimana makanan Anda?</h3>
                <p style="font-size:0.9rem; color:var(--muted);">Bantu kami menjadi lebih baik dengan memberikan ulasan singkat!</p>
            </div>
            <form action="{{ route('orders.review.store', $order) }}" method="POST">
                @csrf
                <div style="display:flex; justify-content:center; gap:0.5rem; margin-bottom: 1rem; flex-direction:row-reverse; font-size: 2rem;" class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required style="display:none"><label for="star5" style="cursor:pointer; color:#d1d5db;">★</label>
                    <input type="radio" id="star4" name="rating" value="4" style="display:none"><label for="star4" style="cursor:pointer; color:#d1d5db;">★</label>
                    <input type="radio" id="star3" name="rating" value="3" style="display:none"><label for="star3" style="cursor:pointer; color:#d1d5db;">★</label>
                    <input type="radio" id="star2" name="rating" value="2" style="display:none"><label for="star2" style="cursor:pointer; color:#d1d5db;">★</label>
                    <input type="radio" id="star1" name="rating" value="1" style="display:none"><label for="star1" style="cursor:pointer; color:#d1d5db;">★</label>
                </div>
                <div class="form-group">
                    <textarea name="comment" rows="3" placeholder="Ceritakan pengalaman Anda (opsional)..." style="width:100%; padding:0.75rem; border-radius:var(--radius-md); border:1px solid var(--border); background:var(--bg2); color:var(--text); resize:vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-gold btn-block" style="margin-top: 1rem;">Kirim Ulasan</button>
            </form>
            <style>
                .star-rating label:hover,
                .star-rating label:hover ~ label,
                .star-rating input:checked ~ label { color: var(--gold) !important; }
            </style>
        </div>
    @elseif($order->isCompleted() && $order->review()->exists())
        <div class="card" style="margin-top: 2rem; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🙏</div>
            <h3 style="font-size: 1.125rem;">Terima kasih atas ulasan Anda!</h3>
            <p style="color:var(--muted); font-size:0.9rem;">Kami sangat menghargai feedback Anda.</p>
        </div>
    @endif
@endsection

@section('scripts')
    @if(env('MIDTRANS_IS_PRODUCTION', false))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', async function () {
                    try {
                        payButton.disabled = true;
                        payButton.innerHTML = '⏳ Memproses...';
                        
                        const response = await fetch('{{ route('orders.snap_token', $order->id) }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            if (data.gateway === 'doku' && data.payment_url) {
                                // Redirect to DOKU Checkout URL
                                window.location.href = data.payment_url;
                            } else if (data.gateway === 'midtrans' && data.snap_token) {
                                // Fallback: Show Midtrans Snap Popup
                                window.snap.pay(data.snap_token, {
                                    onSuccess: async function(result){
                                        // Beri tahu pengguna bahwa pembayaran berhasil diproses Midtrans
                                        payButton.innerHTML = '⏳ Memverifikasi Pembayaran...';
                                        
                                        try {
                                            // Panggil endpoint kita untuk mengecek status terbaru langsung ke Midtrans (Manual Sync)
                                            await fetch('{{ route('orders.check_status') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    order_id: result.order_id
                                                })
                                            });
                                        } catch (e) {
                                            console.error('Gagal sinkronisasi status', e);
                                        }

                                        alert("Pembayaran berhasil!");
                                        window.location.reload();
                                    },
                                    onPending: function(result){
                                        alert("Menunggu pembayaran Anda!");
                                        window.location.reload();
                                    },
                                    onError: function(result){
                                        alert("Pembayaran gagal!");
                                        payButton.disabled = false;
                                        payButton.innerHTML = '💳 Bayar Non-Tunai';
                                    },
                                    onClose: function(){
                                        payButton.disabled = false;
                                        payButton.innerHTML = '💳 Bayar Non-Tunai';
                                    }
                                });
                            } else {
                                alert('Format balasan dari server tidak valid.');
                                payButton.disabled = false;
                                payButton.innerHTML = '💳 Bayar Non-Tunai';
                            }
                        } else {
                            alert(data.message || 'Gagal mendapatkan token pembayaran');
                            payButton.disabled = false;
                            payButton.innerHTML = '💳 Bayar Non-Tunai';
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan sistem di browser: ' + error.message);
                        console.error('Fetch error:', error);
                        payButton.disabled = false;
                        payButton.innerHTML = '💳 Bayar Non-Tunai';
                    }
                });

                @if(request()->query('auto_pay'))
                    // Hapus auto_pay dari URL agar saat di-reload modal tidak terbuka lagi
                    const url = new URL(window.location);
                    url.searchParams.delete('auto_pay');
                    window.history.replaceState({}, '', url);

                    // Trigger otomatis ketika halaman baru saja dibuat (auto_pay=1)
                    payButton.click();
                @endif
            }
        });

        function printInvoice() {
            const invoiceHTML = `
            <!DOCTYPE html>
            <html lang="id">
                <head>
                <meta charset="UTF-8">
                <title>Struk Pembayaran #{{ $order->order_number }}</title>
                <style>
                    @page { size: 80mm auto; margin: 0; }
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body { background: #fff !important; color: #000 !important; font-family: monospace; font-size: 11px; width: 80mm; margin: 0 auto; padding: 15px; }
                    .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
                    .brand { font-size: 16px; font-weight: bold; }
                    .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                    .table th, .table td { text-align: left; vertical-align: top; padding: 2px 0; }
                    .table .right { text-align: right; }
                    .totals { border-top: 1px dashed #000; padding-top: 5px; }
                    .flex { display: flex; justify-content: space-between; margin-bottom: 3px; }
                    .grand { font-weight: bold; font-size: 13px; border-top: 1px solid #000; margin-top: 5px; padding-top: 5px; }
                    .center { text-align: center; }
                </style>
                </head>
                <body>
                    <div class="header">
                        <div class="brand">KUMAW DIMSUM</div>
                        <div style="font-size: 9px; margin-top: 3px;">Jl. Dimsum Enak No. 88</div>
                        <div style="margin-top: 8px;">Order: {{ $order->order_number }}</div>
                        <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <div class="flex"><span>Pelanggan:</span> <span>{{ $order->customer_name ?? 'Guest' }}</span></div>
                        <div class="flex"><span>Tipe:</span> <span>{{ match($order->type) { 'dine_in'=>'Makan di Tempat', 'takeaway'=>'Bawa Pulang', default=>ucfirst($order->type) } }}</span></div>
                        @if($order->table_number)
                        <div class="flex"><span>Meja:</span> <span>{{ $order->table_number }}</span></div>
                        @endif
                    </div>

                    <div class="table-wrapper">
<table class="table">
                        @foreach($order->items as $item)
                        <tr>
                            <td colspan="2">{{ $item->menu_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </table>
</div>

                    <div class="totals">
                        <div class="flex"><span>Subtotal:</span> <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex"><span>Pajak:</span> <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span></div>
                        <div class="flex grand"><span>TOTAL:</span> <span>{{ $order->formattedTotal }}</span></div>
                        <br>
                        <div class="flex"><span>Metode:</span> <span>{{ strtoupper($order->payment_method) }}</span></div>
                        <div class="flex"><span>Status:</span> <span>LUNAS ({{ $order->paid_at ? $order->paid_at->format('H:i') : '' }})</span></div>
                    </div>

                    <div class="center" style="margin-top: 20px; font-size: 10px;">
                        Terima kasih!<br>
                        Struk ini adalah bukti pembayaran sah.
                    </div>
                </body>
            </html>`;

            const pw = window.open('', '_blank', 'width=350,height=600');
            pw.document.write(invoiceHTML);
            pw.document.close();
            pw.onload = () => { pw.focus(); pw.print(); pw.close(); };
            setTimeout(() => { try { pw.focus(); pw.print(); pw.close(); } catch(e) {} }, 800);
        }

        // Auto-refresh polling for status updates
        @if(!in_array($order->status, ['completed', 'cancelled']))
        document.addEventListener('DOMContentLoaded', function() {
            let currentStatus = '{{ $order->status }}';
            setInterval(async () => {
                try {
                    const res = await fetch('{{ route('orders.show', $order->id) }}', {
                        headers: { 
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.status && data.status !== currentStatus) {
                            window.location.reload();
                        }
                    }
                } catch (e) { 
                    console.error('Failed to poll status', e); 
                }
            }, 10000); // Check every 10 seconds
        });
        @endif
    </script>
@endsection
