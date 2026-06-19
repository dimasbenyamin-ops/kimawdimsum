@extends('layouts.admin')

@section('title', 'Buka Shift')

@section('content')
<div class="content-header" style="padding: 1.5rem 1.5rem 0;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--gold);">Mulai Shift Baru</h1>
    <p style="color: var(--muted); margin-top: 0.25rem;">Silakan masukkan modal awal (uang di laci kasir) sebelum mulai menerima pesanan.</p>
</div>

<div class="content-body" style="padding: 1.5rem;">
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; max-width: 500px;">
        <form action="{{ route('cashier.shifts.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="initial_cash" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text);">Modal Awal (Rp) *</label>
                <input type="number" 
                       id="initial_cash" 
                       name="initial_cash" 
                       required 
                       min="0"
                       class="form-control"
                       placeholder="Misal: 100000"
                       style="width: 100%; padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--bg); color: var(--text);">
                @error('initial_cash')
                    <div style="color: var(--error); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" style="width: 100%; padding: 0.75rem; background: var(--gold); color: #fff; border: none; border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                <i class="bi bi-play-circle-fill"></i> Buka Shift Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
