@extends('layouts.admin')

@section('title', 'Dashboard Kasir – Kumaw Dimsum')

@section('styles')
<style>
    /* ---- Stats strip ---- */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-tile {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.125rem 1.25rem;
    }
    .stat-tile-value { font-size: 1.75rem; font-weight: 800; color: var(--gold); line-height: 1; }
    .stat-tile-label { font-size: 0.8125rem; color: var(--muted); margin-top: 0.375rem; }

    /* ---- Kanban columns ---- */
    .kanban {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 1100px) { .kanban { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px)  { .kanban { grid-template-columns: 1fr; } }

    .kanban-col { }
    .kanban-col-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 0.875rem;
        border-radius: var(--radius) var(--radius) 0 0;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
    }
    .col-pending   { background: rgba(251,191,36,0.12); color: var(--warning); }
    .col-confirmed { background: rgba(96,165,250,0.12); color: var(--info); }
    .col-preparing { background: rgba(167,139,250,0.12); color: #a78bfa; }
    .col-ready     { background: rgba(52,211,153,0.12); color: var(--success); }

    .count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px; height: 20px;
        border-radius: 50%;
        font-size: 0.75rem;
        background: rgba(255,255,255,0.12);
    }

    /* ---- Order ticket ---- */
    .ticket {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 0.875rem;
        transition: border-color 0.2s;
    }
    .ticket:hover { border-color: rgba(245,158,11,0.25); }

    .ticket-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .ticket-num {
        font-size: 0.875rem;
        font-weight: 700;
        font-family: monospace;
        color: var(--gold);
    }
    .ticket-time { font-size: 0.75rem; color: var(--muted); }

    .ticket-customer {
        font-size: 0.8125rem;
        color: var(--muted);
        margin-bottom: 0.625rem;
    }

    .ticket-type {
        display: inline-block;
        font-size: 0.75rem;
        padding: 0.125rem 0.5rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--border);
    }

    .ticket-items {
        font-size: 0.8125rem;
        color: var(--muted);
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }
    .ticket-items li { display: flex; justify-content: space-between; }

    .ticket-total {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text);
        border-top: 1px solid var(--border);
        padding-top: 0.625rem;
        display: flex;
        justify-content: space-between;
    }

    /* ---- Action form ---- */
    .action-form { margin-top: 0.875rem; }
    .action-form select {
        font-size: 0.8125rem;
        padding: 0.5rem 0.625rem;
        border-radius: 8px;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .action-form .btn { font-size: 0.8125rem; padding: 0.45rem 0.875rem; }

    .no-orders {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--muted);
        font-size: 0.875rem;
        border: 1px dashed var(--border);
        border-radius: var(--radius);
    }
    .no-orders .no-icon { font-size: 2rem; margin-bottom: 0.5rem; }

    /* ---- Auto-refresh indicator ---- */
    .refresh-bar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        font-size: 0.8125rem;
        color: var(--muted);
    }
    .refresh-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--success);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    /* ================================================================
       INVOICE MODAL
    ================================================================ */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.75);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.active { display: flex; }

    .invoice-modal {
        background: #fff;
        color: #1a1a1a;
        border-radius: 12px;
        width: 100%;
        max-width: 420px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        animation: slideUp 0.25s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .invoice-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        text-align: center;
    }
    .invoice-brand {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #f5a623;
    }
    .invoice-tagline {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.6);
        margin-top: 0.2rem;
    }
    .invoice-divider {
        border: none;
        border-top: 1px dashed rgba(255,255,255,0.25);
        margin: 0.875rem 0;
    }
    .invoice-number {
        font-family: monospace;
        font-size: 0.9rem;
        color: #f5a623;
        font-weight: 700;
    }
    .invoice-date {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        margin-top: 0.2rem;
    }

    .invoice-body { padding: 1.25rem; }

    .invoice-customer-block {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    .invoice-customer-block .label { color: #6b7280; font-size: 0.75rem; }
    .invoice-customer-block .value { font-weight: 600; color: #111; }

    .invoice-items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
        margin-bottom: 1rem;
    }
    .invoice-items-table th {
        text-align: left;
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.375rem 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .invoice-items-table td {
        padding: 0.5rem 0.5rem;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        vertical-align: top;
    }
    .invoice-items-table td:last-child { text-align: right; }
    .invoice-items-table th:last-child { text-align: right; }

    .invoice-totals {
        border-top: 2px solid #e5e7eb;
        padding-top: 0.75rem;
        font-size: 0.875rem;
    }
    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0;
        color: #4b5563;
    }
    .invoice-total-row.grand {
        font-size: 1rem;
        font-weight: 800;
        color: #111;
        border-top: 1px dashed #d1d5db;
        margin-top: 0.5rem;
        padding-top: 0.625rem;
    }
    .invoice-total-row.paid-badge {
        justify-content: center;
        margin-top: 0.75rem;
    }
    .paid-stamp {
        background: #dcfce7;
        color: #15803d;
        border: 2px solid #86efac;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.9rem;
        padding: 0.35rem 1.5rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .invoice-footer {
        text-align: center;
        padding: 1rem;
        border-top: 1px dashed #e5e7eb;
        font-size: 0.75rem;
        color: #9ca3af;
    }
    .invoice-footer strong { color: #f5a623; }

    /* Modal action bar */
    .modal-actions {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 0 0 12px 12px;
        justify-content: flex-end;
    }
    .modal-btn {
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .modal-btn-ghost {
        background: #fff;
        border: 1px solid #d1d5db;
        color: #374151;
    }
    .modal-btn-ghost:hover { background: #f3f4f6; }
    .modal-btn-print {
        background: #1d4ed8;
        color: #fff;
    }
    .modal-btn-print:hover { background: #1e40af; }
    .modal-btn-confirm {
        background: #16a34a;
        color: #fff;
    }
    .modal-btn-confirm:hover { background: #15803d; }

    /* No @media print needed — printing is handled via a dedicated popup window in JS */
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>📊 Dashboard Kasir</h1>
            <p>Kelola pesanan masuk secara real-time</p>
        </div>
        <div style="display:flex;gap:0.5rem">
            <button onclick="location.reload()" class="btn btn-ghost btn-sm" id="btn-refresh">🔄 Refresh</button>
        </div>
    </div>

    {{-- Auto-refresh indicator --}}
    <div class="refresh-bar">
        <div class="refresh-dot"></div>
        <span>Halaman ini otomatis refresh setiap 30 detik</span>
        <span id="countdown" style="margin-left:auto;color:var(--gold);font-weight:600">30d</span>
    </div>

    {{-- Stats strip --}}
    <div class="stats-strip">
        @php
            $totalActive = 0;
            foreach (['pending_payment', 'confirmed', 'preparing', 'ready'] as $s) {
                $totalActive += ($activeOrders[$s] ?? collect())->count();
            }
        @endphp
        <div class="stat-tile">
            <div class="stat-tile-value">{{ $totalActive }}</div>
            <div class="stat-tile-label">Pesanan Aktif</div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile-value">{{ $todayCompleted }}</div>
            <div class="stat-tile-label">Selesai Hari Ini</div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="stat-tile-label">Pendapatan Hari Ini</div>
        </div>
    </div>

    {{-- Kanban board --}}
    <div class="kanban">
        @foreach($statusOrder as $status)
            @php
                $orders  = $activeOrders[$status] ?? collect();
                $configs = [
                    'pending_payment' => ['icon' => '💳', 'label' => 'Belum Bayar', 'cls' => 'col-pending'],
                    'confirmed' => ['icon' => '✅', 'label' => 'Dikonfirmasi', 'cls' => 'col-confirmed'],
                    'preparing' => ['icon' => '👨‍🍳', 'label' => 'Diproses', 'cls' => 'col-preparing'],
                    'ready'     => ['icon' => '🔔', 'label' => 'Siap', 'cls' => 'col-ready'],
                ];
                $cfg = $configs[$status];
            @endphp
            <div class="kanban-col">
                <div class="kanban-col-header {{ $cfg['cls'] }}">
                    {{ $cfg['icon'] }} {{ $cfg['label'] }}
                    <span class="count-badge">{{ $orders->count() }}</span>
                </div>

                @forelse($orders as $order)
                    <div class="ticket" id="ticket-{{ $order->id }}">
                        <div class="ticket-header">
                            <div>
                                <div class="ticket-num">{{ $order->order_number }}</div>
                                <div class="ticket-time">{{ $order->created_at->format('H:i') }} · {{ $order->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="ticket-type">
                                {{ match($order->type) { 'dine_in'=>'🍽️ Makan', 'takeaway'=>'📦 Bawa', 'delivery'=>'🛵 Antar', default=>$order->type } }}
                                @if($order->table_number) #{{ $order->table_number }} @endif
                            </span>
                        </div>

                        <div class="ticket-customer">
                            <div>👤 {{ $order->customer_name ?? 'Guest' }}</div>
                            @if($order->phone_number)
                                <div style="font-size:0.7rem; color:var(--info);"><a href="tel:{{ $order->phone_number }}" style="color: inherit; text-decoration: none;">📞 {{ $order->phone_number }}</a></div>
                            @endif
                        </div>

                        <ul class="ticket-items">
                            @foreach($order->items as $item)
                                <li>
                                    <span>{{ $item->menu_name }}</span>
                                    <span>×{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if($order->customer_notes)
                            <div style="font-size:0.75rem;color:var(--warning);margin-bottom:0.5rem;font-style:italic">
                                📝 {{ $order->customer_notes }}
                            </div>
                        @endif

                        <div class="ticket-total">
                            <span>Total</span>
                            <span>{{ $order->formattedTotal }}</span>
                        </div>

                        {{-- Action buttons --}}
                        <div class="action-form">
                            @php
                                $nextSteps = [
                                    'pending_payment' => [
                                        ['status'=>'confirmed','label'=>'🖨️ Cetak & Lunas','cls'=>'btn-success','invoice'=>true],
                                        ['status'=>'cancelled','label'=>'❌ Tolak','cls'=>'btn-danger','invoice'=>false],
                                    ],
                                    'confirmed' => [['status'=>'preparing','label'=>'👨‍🍳 Proses','cls'=>'btn-gold','invoice'=>false],['status'=>'cancelled','label'=>'❌ Batal','cls'=>'btn-danger','invoice'=>false]],
                                    'preparing' => [['status'=>'ready','label'=>'🔔 Siap','cls'=>'btn-gold','invoice'=>false]],
                                    'ready'     => [['status'=>'completed','label'=>'🎉 Selesai','cls'=>'btn-success','invoice'=>false]],
                                ];
                                $buttons = $nextSteps[$status] ?? [];
                            @endphp

                            @foreach($buttons as $btn)
                                @if(!empty($btn['invoice']))
                                    {{-- Invoice Button: opens modal instead of submitting directly --}}
                                    <button
                                        type="button"
                                        class="btn {{ $btn['cls'] }} btn-sm"
                                        id="action-{{ $order->id }}-{{ $btn['status'] }}"
                                        style="margin-bottom:0.375rem;width:100%"
                                        onclick="openInvoice({{ json_encode([
                                            'orderId'      => $order->id,
                                            'orderNumber'  => $order->order_number,
                                            'customerName' => $order->customer_name ?? 'Guest',
                                            'phone'        => $order->phone_number,
                                            'type'         => match($order->type){ 'dine_in'=>'Makan di Tempat', 'takeaway'=>'Bawa Pulang', 'delivery'=>'Delivery', default=>$order->type },
                                            'tableNumber'  => $order->table_number,
                                            'notes'        => $order->customer_notes,
                                            'paymentMethod'=> $order->payment_method,
                                            'subtotal'     => $order->subtotal,
                                            'tax'          => $order->tax_amount,
                                            'total'        => $order->total_amount,
                                            'createdAt'    => $order->created_at->format('d/m/Y H:i'),
                                            'items'        => $order->items->map(fn($i) => [
                                                'name'     => $i->menu_name,
                                                'qty'      => $i->quantity,
                                                'price'    => $i->unit_price,
                                                'subtotal' => $i->unit_price * $i->quantity,
                                            ])->toArray(),
                                            'actionUrl'    => route('cashier.orders.updateStatus', $order),
                                        ]) }})"
                                    >
                                        {{ $btn['label'] }}
                                    </button>
                                @else
                                    <form method="POST"
                                          action="{{ route('cashier.orders.updateStatus', $order) }}"
                                          style="display:inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $btn['status'] }}">
                                        @if($btn['status'] === 'completed' && !$order->isPaid())
                                            <select name="payment_method" style="margin-bottom:0.5rem">
                                                <option value="cash" {{ $order->payment_method === 'cash' ? 'selected' : '' }}>💵 Tunai</option>
                                                <option value="transfer" {{ $order->payment_method === 'transfer' ? 'selected' : '' }}>🏦 Transfer</option>
                                                <option value="qris" {{ $order->payment_method === 'qris' ? 'selected' : '' }}>📱 QRIS</option>
                                            </select>
                                        @endif
                                        <button
                                            type="submit"
                                            class="btn {{ $btn['cls'] }} btn-sm"
                                            id="action-{{ $order->id }}-{{ $btn['status'] }}"
                                            style="margin-bottom:0.375rem;width:100%"
                                        >
                                            {{ $btn['label'] }}
                                        </button>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="no-orders">
                        <div class="no-icon">☕</div>
                        Tidak ada pesanan
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>

    {{-- ================================================================
         INVOICE MODAL
    ================================================================ --}}
    <div class="modal-overlay" id="invoice-modal" role="dialog" aria-modal="true" aria-labelledby="invoice-title">
        <div class="invoice-modal" id="invoice-printable">

            {{-- HEADER --}}
            <div class="invoice-header">
                <div class="invoice-brand">🥟 Kumaw Dimsum</div>
                <div class="invoice-tagline">Jl. Dimsum Enak No. 88 · Telp. (021) 888-0000</div>
                <hr class="invoice-divider">
                <div class="invoice-number" id="inv-number">—</div>
                <div class="invoice-date" id="inv-date">—</div>
            </div>

            {{-- BODY --}}
            <div class="invoice-body">

                {{-- Customer info --}}
                <div class="invoice-customer-block">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                        <div>
                            <div class="label">Pelanggan</div>
                            <div class="value" id="inv-customer">—</div>
                        </div>
                        <div>
                            <div class="label">Jenis Pesanan</div>
                            <div class="value" id="inv-type">—</div>
                        </div>
                    </div>
                    <div id="inv-table-row" style="margin-top:0.5rem;display:none">
                        <div class="label">Nomor Meja</div>
                        <div class="value" id="inv-table">—</div>
                    </div>
                    <div id="inv-notes-row" style="margin-top:0.5rem;display:none">
                        <div class="label">Catatan</div>
                        <div class="value" id="inv-notes" style="font-style:italic;color:#6b7280">—</div>
                    </div>
                </div>

                {{-- Items table --}}
                <table class="invoice-items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style="text-align:center">Qty</th>
                            <th style="text-align:right">Harga</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="inv-items-body">
                        {{-- Populated by JS --}}
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="invoice-totals">
                    <div class="invoice-total-row">
                        <span>Subtotal</span>
                        <span id="inv-subtotal">—</span>
                    </div>
                    <div class="invoice-total-row">
                        <span>Pajak (11%)</span>
                        <span id="inv-tax">—</span>
                    </div>
                    <div class="invoice-total-row grand">
                        <span>TOTAL</span>
                        <span id="inv-total">—</span>
                    </div>
                    <div class="invoice-total-row">
                        <span>Metode Bayar</span>
                        <span id="inv-payment" style="font-weight:600;text-transform:uppercase">—</span>
                    </div>
                    <div class="invoice-total-row paid-badge">
                        <span class="paid-stamp">✓ LUNAS</span>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="invoice-footer">
                Terima kasih telah memesan di<br>
                <strong>Kumaw Dimsum</strong> 🥟<br>
                <span style="font-size:0.7rem">Simpan struk ini sebagai bukti pembayaran</span>
            </div>

            {{-- Modal action bar (hidden when printing) --}}
            <div class="modal-actions">
                <button class="modal-btn modal-btn-ghost" onclick="closeInvoice()" id="btn-close-modal">✕ Tutup</button>
                <button class="modal-btn modal-btn-print" onclick="printInvoice()" id="btn-print-invoice">🖨️ Cetak Struk</button>
                <button class="modal-btn modal-btn-confirm" id="btn-confirm-payment" onclick="submitPayment()">✅ Konfirmasi Pembayaran</button>
            </div>
        </div>
    </div>

    {{-- Hidden form submitted when confirming payment --}}
    <form id="confirm-form" method="POST" action="" style="display:none">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="confirmed">
        <input type="hidden" name="payment_method" id="confirm-payment-method" value="cash">
    </form>

@endsection

@section('scripts')
<script>
    /* ---- Auto-refresh countdown ---- */
    let secs = 30;
    const cd = document.getElementById('countdown');
    setInterval(() => {
        secs--;
        if (cd) cd.textContent = secs + 'd';
        if (secs <= 0) location.reload();
    }, 1000);

    /* ---- Invoice Modal Logic ---- */
    let _invoiceData = null;

    function formatRp(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function openInvoice(data) {
        _invoiceData = data;

        // Populate header
        document.getElementById('inv-number').textContent = '#' + data.orderNumber;
        document.getElementById('inv-date').textContent   = data.createdAt;

        // Populate customer info
        document.getElementById('inv-customer').textContent = data.customerName;
        document.getElementById('inv-type').textContent     = data.type;

        const tableRow = document.getElementById('inv-table-row');
        if (data.tableNumber) {
            tableRow.style.display = 'block';
            document.getElementById('inv-table').textContent = 'Meja #' + data.tableNumber;
        } else {
            tableRow.style.display = 'none';
        }

        const notesRow = document.getElementById('inv-notes-row');
        if (data.notes) {
            notesRow.style.display = 'block';
            document.getElementById('inv-notes').textContent = data.notes;
        } else {
            notesRow.style.display = 'none';
        }

        // Populate items
        const tbody = document.getElementById('inv-items-body');
        tbody.innerHTML = '';
        data.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.name}</td>
                <td style="text-align:center">${item.qty}</td>
                <td style="text-align:right">${formatRp(item.price)}</td>
                <td style="text-align:right">${formatRp(item.subtotal)}</td>
            `;
            tbody.appendChild(tr);
        });

        // Populate totals
        document.getElementById('inv-subtotal').textContent = formatRp(data.subtotal);
        document.getElementById('inv-tax').textContent      = formatRp(data.tax);
        document.getElementById('inv-total').textContent    = formatRp(data.total);

        // Payment method label
        const pmLabels = { cash: '💵 Tunai', qris: '📱 QRIS', transfer: '🏦 Transfer', unpaid: '—' };
        document.getElementById('inv-payment').textContent = pmLabels[data.paymentMethod] ?? data.paymentMethod;

        // Set hidden form target
        document.getElementById('confirm-form').action = data.actionUrl;
        document.getElementById('confirm-payment-method').value = data.paymentMethod;

        // Show modal
        const overlay = document.getElementById('invoice-modal');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeInvoice() {
        document.getElementById('invoice-modal').classList.remove('active');
        document.body.style.overflow = '';
        _invoiceData = null;
    }

    function printInvoice() {
        // Clone the rendered invoice HTML
        const invoiceEl = document.getElementById('invoice-printable');
        const invoiceHTML = invoiceEl.innerHTML;

        // Open a minimal dedicated print window — nothing else in the DOM
        const pw = window.open('', '_blank', 'width=420,height=700');
        pw.document.write(`
        <!DOCTYPE html>
        <html lang="id">
            <head>
            <meta charset="UTF-8">
            <title>Struk Pembayaran</title>
            <style>
                @page { size: 80mm auto; margin: 0; }

                * { box-sizing: border-box; margin: 0; padding: 0; }

                body {
                background: #fff !important;
                color: #000000 !important;
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 9.5pt;
                width: 80mm;
                margin: 0 auto;
                }

                /* ---- Invoice Header ---- */
                .invoice-header {
                background: transparent !important;
                color: #000000 !important;
                padding: 14px;
                text-align: center;
                border-bottom: 2px solid #000;
                }
                .invoice-brand { font-size: 15px; font-weight: 800; letter-spacing: .05em; color: #000000 !important; }
                .invoice-tagline { font-size: 9px; color: #444 !important; margin-top: 2px; }
                .invoice-divider { border: none; border-top: 1px dashed #888; margin: 8px 0; }
                .invoice-number { font-family: monospace; font-size: 11px; color: #000000 !important; font-weight: 700; }
                .invoice-date { font-size: 9px; color: #555 !important; margin-top: 2px; }

                /* ---- Body ---- */
                .invoice-body { padding: 10px; }

                .invoice-customer-block {
                background: #f8f9fa;
                border-radius: 6px;
                padding: 8px 10px;
                margin-bottom: 10px;
                font-size: 10px;
                }
                .invoice-customer-block .label { color: #6b7280; font-size: 9px; }
                .invoice-customer-block .value { font-weight: 600; color: #111; }

                .invoice-items-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
                .invoice-items-table th {
                text-align: left;
                font-size: 9px;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: .05em;
                padding: 3px 4px;
                border-bottom: 1px solid #e5e7eb;
                }
                .invoice-items-table td {
                padding: 3px 4px !important;
                border-bottom: 1px solid #f3f4f6;
                color: #000000 !important;
                vertical-align: top;
                }
                .invoice-items-table td:last-child,
                .invoice-items-table th:last-child { text-align: right; }

                .invoice-totals { border-top: 2px solid #e5e7eb; padding-top: 8px; font-size: 10px; }
                .invoice-total-row { display: flex; justify-content: space-between; padding: 2px 0; color: #4b5563; }
                .invoice-total-row.grand {
                font-size: 12px; font-weight: 800; color: #111;
                border-top: 1px dashed #d1d5db; margin-top: 5px; padding-top: 6px;
                }
                .invoice-total-row.paid-badge { justify-content: center; margin-top: 8px; }
                .paid-stamp {
                background: #dcfce7; color: #15803d;
                border: 2px solid #86efac; border-radius: 5px;
                font-weight: 800; font-size: 11px;
                padding: 3px 16px; letter-spacing: .08em; text-transform: uppercase;
                }

                .invoice-footer {
                text-align: center; padding: 10px;
                border-top: 1px dashed #e5e7eb;
                font-size: 9px; color: #9ca3af;
                }
                .invoice-footer strong { color: #f5a623; }

                /* Hide action buttons */
                .modal-actions { display: none !important; }
            </style>
            </head>
            <body>
            ${invoiceHTML}
            </body>
        </html>`);
        pw.document.close();

        // Wait for resources then print
        pw.onload = () => { pw.focus(); pw.print(); pw.close(); };
        // Fallback for browsers that fire onload before write finishes
        setTimeout(() => { try { pw.focus(); pw.print(); pw.close(); } catch(e) {} }, 600);
    }

    function submitPayment() {
        // Disable button to prevent double-submit
        const btn = document.getElementById('btn-confirm-payment');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        document.getElementById('confirm-form').submit();
    }

    // Close modal when clicking backdrop
    document.getElementById('invoice-modal').addEventListener('click', function(e) {
        if (e.target === this) closeInvoice();
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeInvoice();
    });
</script>
@endsection
