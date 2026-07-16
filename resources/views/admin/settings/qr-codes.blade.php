@extends('layouts.admin')

@section('title', 'Cetak QR Code Meja')

@section('styles')
<style>
    /* Styling for the input form */
    .setup-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2.5rem;
    }

    /* Premium Dark Table Tent Design */
    .qr-card {
        background: #111111;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        border: 2px solid #2a2a2a;
        color: #fff;
    }
    
    .qr-card-header {
        background: linear-gradient(135deg, #2a2a2a, #111111);
        width: 100%;
        padding: 2rem 1rem 1.5rem 1rem;
        border-bottom: 2px solid #d4af37; /* Gold accent border */
    }

    .qr-card-header h2 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #d4af37; /* Gold text */
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    
    .qr-card-header p {
        margin: 0.5rem 0 0 0;
        font-size: 0.9rem;
        color: #a3a3a3;
        letter-spacing: 1px;
    }

    .qr-card-body {
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        background: radial-gradient(circle at center, #1f1f1f 0%, #111111 100%);
    }

    .qr-card-body h3 {
        margin: 0 0 1.5rem 0;
        font-size: 2.2rem;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .qr-code-container {
        background: #fff;
        padding: 12px;
        border-radius: 16px;
        box-shadow: 0 0 25px rgba(212, 175, 55, 0.2); /* Subtle gold glow */
        margin-bottom: 1.5rem;
        border: 3px solid #d4af37; /* Gold border around QR */
        transition: transform 0.3s ease;
    }

    .qr-code-container:hover {
        transform: scale(1.05);
    }

    .qr-code-container svg {
        display: block;
        width: 180px;
        height: 180px;
        border-radius: 4px;
    }

    .qr-instructions {
        font-size: 0.85rem;
        color: #a3a3a3;
        margin-bottom: 1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .qr-instructions strong {
        color: #d4af37;
    }

    .qr-link {
        font-size: 0.75rem;
        color: #737373;
        word-break: break-all;
        background: #000000;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        width: 100%;
        border: 1px solid #2a2a2a;
    }

    /* Print Styles: adapt dark mode for printer ink if necessary, but here we keep it premium dark */
    @media print {
        body {
            background: #fff !important;
        }
        body * {
            visibility: hidden;
        }
        .qr-grid, .qr-grid * {
            visibility: visible;
        }
        .qr-grid {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 per row for A4 */
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        .qr-card {
            page-break-inside: avoid;
            margin-bottom: 20px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-shadow: none !important;
            border: 2px solid #000 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div>
        <h1>Cetak QR Code Meja</h1>
        <p>Hasilkan dan cetak QR Code meja. Desain ini menggunakan tema gelap eksklusif Kumaw Dimsum.</p>
    </div>
    <div>
        <button type="button" class="btn btn-gold" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak QR
        </button>
    </div>
</div>

<div class="setup-card d-print-none" style="max-width: 500px;">
    <form action="{{ route('admin.settings.qr_codes') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label for="tables">Jumlah Meja yang Tersedia</label>
            <input type="number" id="tables" name="tables" value="{{ $tableCount }}" min="1" max="500" class="form-control" style="font-size: 1.1rem; padding: 0.75rem;" required>
        </div>
        <div>
            <button type="submit" class="btn btn-gold" style="padding: 0.75rem 1.5rem;">
                <i class="bi bi-arrow-repeat"></i> Generate
            </button>
        </div>
    </form>
</div>

<div class="qr-grid">
    @for($i = 1; $i <= $tableCount; $i++)
        <div class="qr-card">
            <div class="qr-card-header">
                <h2>Kumaw Dimsum</h2>
                <p>SCAN UNTUK PESAN MENU</p>
            </div>
            <div class="qr-card-body">
                <h3>MEJA {{ $i }}</h3>
                <div class="qr-code-container">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(0)->generate(route('scan.table', $i)) !!}
                </div>
                <div class="qr-instructions">
                    Buka <strong>Kamera HP</strong> Anda<br>
                    Arahkan ke QR Code di atas<br>
                    <strong>Pesan dan Nikmati!</strong>
                </div>
                <div class="qr-link">{{ route('scan.table', $i) }}</div>
            </div>
        </div>
    @endfor
</div>
@endsection

@section('scripts')
<script>
    // No JS needed for QR rendering anymore!
</script>
@endsection
