@extends('layouts.admin')

@section('title', 'Tutup Shift')

@section('content')
<div class="content-header" style="padding: 1.5rem 1.5rem 0;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--gold);">Tutup Shift</h1>
    <p style="color: var(--muted); margin-top: 0.25rem;">Hitung uang fisik di laci dan akhiri shift Anda.</p>
</div>

<div class="content-body" style="padding: 1.5rem;">
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; max-width: 600px;">
        
        <div style="margin-bottom: 1.5rem; display: grid; gap: 1rem; grid-template-columns: 1fr 1fr;">
            <div style="background: var(--bg); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <div style="color: var(--muted); font-size: 0.875rem; margin-bottom: 0.25rem;">Waktu Mulai</div>
                <div style="font-weight: 600;">{{ $activeShift->start_time->format('d M Y, H:i') }}</div>
            </div>
            <div style="background: var(--bg); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <div style="color: var(--muted); font-size: 0.875rem; margin-bottom: 0.25rem;">Modal Awal</div>
                <div style="font-weight: 600;">Rp {{ number_format($activeShift->initial_cash, 0, ',', '.') }}</div>
            </div>
            <div style="background: var(--bg); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <div style="color: var(--muted); font-size: 0.875rem; margin-bottom: 0.25rem;">Penjualan Tunai</div>
                <div style="font-weight: 600; color: var(--success);">+ Rp {{ number_format($cashTotal, 0, ',', '.') }}</div>
            </div>
            <div style="background: var(--bg); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <div style="color: var(--muted); font-size: 0.875rem; margin-bottom: 0.25rem;">Penjualan QRIS</div>
                <div style="font-weight: 600; color: var(--info);">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</div>
            </div>
        </div>

        <div style="background: var(--gold-dim); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--gold-border); margin-bottom: 1.5rem; text-align: center;">
            <div style="color: var(--gold); font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Expected Cash (Tunai Seharusnya)</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--gold);">Rp {{ number_format($expectedCash, 0, ',', '.') }}</div>
            <div style="font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem;">(Modal Awal + Penjualan Tunai)</div>
        </div>

        <hr style="border-color: var(--border); margin: 1.5rem 0;">

        <form action="{{ route('cashier.shifts.close') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="final_cash" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text);">Jumlah Uang Fisik Aktual (Rp) *</label>
                <p style="font-size: 0.8125rem; color: var(--muted); margin-bottom: 0.5rem;">Hitung seluruh uang tunai yang ada di laci saat ini dan masukkan jumlahnya di bawah.</p>
                <input type="number" 
                       id="final_cash" 
                       name="final_cash" 
                       required 
                       min="0"
                       class="form-control"
                       placeholder="Misal: 550000"
                       style="width: 100%; padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--bg); color: var(--text); font-size: 1.125rem; font-weight: 600;">
                @error('final_cash')
                    <div style="color: var(--error); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('cashier.dashboard') }}" style="flex: 1; text-align: center; padding: 0.75rem; background: var(--surface); color: var(--text); text-decoration: none; border: 1px solid var(--border); border-radius: var(--radius-sm); font-weight: 600;">Batal</a>
                <button type="submit" style="flex: 2; padding: 0.75rem; background: var(--error); color: #fff; border: none; border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-stop-circle-fill"></i> Akhiri Shift Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
