@extends('layouts.admin')

@section('title', 'Cetak Struk – Kumaw Dimsum')

@section('styles')
<style>
    .receipt-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem;
    }

    /* Modal styling from dashboard adapted for static page */
    .invoice-modal {
        background: #fff;
        color: #1a1a1a;
        border-radius: 12px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin: 0 auto;
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

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 0 0 12px 12px;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .modal-btn {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.35rem;
        text-align: center;
        text-decoration: none;
        box-sizing: border-box;
    }
    .modal-btn-ghost {
        background: #fff;
        border: 1px solid #d1d5db;
        color: #374151;
    }
    .modal-btn-ghost:hover { background: #f3f4f6; color: #374151; }
    
    .modal-btn-primary {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }
    .modal-btn-primary:hover { background: #bae6fd; color: #0369a1; }
    
    .modal-btn-print {
        background: #1d4ed8;
        color: #fff;
    }
    .modal-btn-print:hover { background: #1e40af; color: #fff; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Cetak Struk</h1>
        <p>Pesanan #{{ $order->order_number }}</p>
    </div>
</div>

<div class="receipt-wrapper">
    <div class="invoice-modal" id="invoice-printable">
        {{-- HEADER --}}
        <div class="invoice-header">
            <div class="invoice-brand">{{ \App\Models\Setting::getValue('store_name', 'Kumaw Dimsum') }}</div>
            <div class="invoice-tagline">{{ \App\Models\Setting::getValue('store_address', 'Jl. Dimsum Enak No. 88') }} · Telp. {{ \App\Models\Setting::getValue('store_phone', '(021) 888-0000') }}</div>
            <hr class="invoice-divider">
            <div class="invoice-number">#{{ $order->order_number }}</div>
            <div class="invoice-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>

        {{-- BODY --}}
        <div class="invoice-body">
            {{-- Customer info --}}
            <div class="invoice-customer-block">
                <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                    <div>
                        <div class="label">Pelanggan</div>
                        <div class="value">{{ $order->customer_name }}</div>
                    </div>
                    <div>
                        <div class="label">Jenis Pesanan</div>
                        <div class="value">{{ match($order->type){ 'dine_in'=>'Makan di Tempat', 'takeaway'=>'Bawa Pulang', default=>$order->type } }}</div>
                    </div>
                </div>
                @if($order->table_number)
                <div style="margin-top:0.5rem;">
                    <div class="label">Nomor Meja</div>
                    <div class="value">Meja #{{ $order->table_number }}</div>
                </div>
                @endif
                @if($order->customer_notes)
                <div style="margin-top:0.5rem;">
                    <div class="label">Catatan</div>
                    <div class="value" style="font-style:italic;color:#6b7280">{{ $order->customer_notes }}</div>
                </div>
                @endif
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
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->menu_name }}</td>
                        <td style="text-align:center">{{ $item->quantity }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="invoice-totals">
                <div class="invoice-total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="invoice-total-row">
                    <span>Diskon</span>
                    <span style="color: #ef4444;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="invoice-total-row grand">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="invoice-total-row">
                    <span>Metode Bayar</span>
                    <span style="font-weight:600;text-transform:uppercase">{{ $order->payment_method === 'cash' ? '💵 Tunai' : '📱 QRIS' }}</span>
                </div>
                <div class="invoice-total-row paid-badge">
                    <span class="paid-stamp">✓ LUNAS</span>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="invoice-footer">
            Terima kasih telah memesan di<br>
            <strong>{{ \App\Models\Setting::getValue('store_name', 'Kumaw Dimsum') }}</strong><br>
            <span style="font-size:0.7rem">Simpan struk ini sebagai bukti pembayaran</span>
        </div>

        {{-- Modal action bar (hidden when printing) --}}
        <div class="modal-actions" style="flex-wrap: nowrap;">
            <a href="{{ route('cashier.dashboard') }}" class="modal-btn modal-btn-ghost">🔙 Kembali</a>
            <a href="{{ route('cashier.order.create') }}" class="modal-btn modal-btn-primary">➕ Pesanan Baru</a>
            <button class="modal-btn modal-btn-print" onclick="printInvoice()">🖨️ Cetak Struk</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
