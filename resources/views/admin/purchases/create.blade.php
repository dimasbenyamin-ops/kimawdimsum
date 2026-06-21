@extends('layouts.admin')

@section('title', 'Buat PO Pembelian – Kumaw Dimsum Admin')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>🛒 Buat PO Pembelian</h1>
            <p>Catat pembelian bahan baku untuk menambah stok dan menghitung HPP rata-rata.</p>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.purchases.store') }}">
        @csrf

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.25rem;"></i>
                <div>
                    <strong style="display:block; margin-bottom:0.25rem">Gagal Menyimpan! Periksa isian berikut:</strong>
                    <ul style="margin-left:1.5rem; font-size:0.875rem">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="form-card">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Informasi PO</h2>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem">
                <div class="form-group">
                    <label for="purchase_date">Tanggal Pembelian <span style="color:var(--error)">*</span></label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="supplier_id">Supplier</label>
                    <select id="supplier_id" name="supplier_id" style="width:100%">
                        <option value="">— Opsional (Pilih Supplier) —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Catatan Tambahan</label>
                <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Catatan PO, no referensi, dll..." maxlength="500">
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-card">
            <div style="margin-bottom: 1.5rem; display:flex; justify-content:space-between; align-items:flex-end;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Daftar Barang</h2>
                    <p style="color: var(--muted); font-size: 0.875rem;">Masukkan barang yang dibeli beserta kuantitas dan harga satuan.</p>
                </div>
            </div>

            <div class="table-wrapper" style="margin-bottom:1rem">
                <table style="min-width: 800px">
                    <thead>
                        <tr>
                            <th style="width:40%">Bahan Baku</th>
                            <th style="width:15%">Kuantitas</th>
                            <th style="width:20%">Harga Satuan (Rp)</th>
                            <th style="width:20%">Subtotal (Rp)</th>
                            <th style="width:5%;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="po-items-container">
                        {{-- First Row is Required --}}
                        <tr class="po-item">
                            <td>
                                <select name="items[0][ingredient_id]" class="ingredient-select" required style="width:100%">
                                    <option value="">— Pilih Bahan Baku —</option>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit->abbreviation }}">
                                            {{ $ingredient->name }} ({{ $ingredient->unit->abbreviation }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem">
                                    <input type="number" name="items[0][quantity]" class="input-qty" step="any" min="0.0001" placeholder="0" required style="width:100%">
                                    <span class="unit-label" style="font-size:0.875rem; color:var(--muted); min-width:30px">—</span>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="items[0][unit_price]" class="input-price" step="0.01" min="0" placeholder="0" required style="width:100%">
                            </td>
                            <td>
                                <input type="text" class="input-subtotal" readonly style="width:100%; background:var(--background); border-color:transparent; font-family:monospace" value="0">
                            </td>
                            <td style="text-align:center">
                                <button type="button" class="btn btn-sm btn-ghost remove-item" disabled title="Hapus">
                                    <i class="bi bi-x-lg" style="color:var(--danger)"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="padding:1rem 0">
                                <button type="button" id="btn-add-item" class="btn btn-ghost" style="width:100%; border:1px dashed var(--border)">
                                    <i class="bi bi-plus-lg"></i> Tambah Baris
                                </button>
                            </td>
                        </tr>
                        <tr style="background:var(--surface-hover); font-weight:bold;">
                            <td colspan="3" style="text-align:right">Total Estimasi Pembelian :</td>
                            <td colspan="2" style="font-family:monospace; font-size:1.2rem">Rp <span id="grand-total">0</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div style="display:flex;gap:0.75rem; margin-bottom:3rem">
            <button type="submit" class="btn btn-gold">
                <i class="bi bi-save"></i> Simpan Draft PO
            </button>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 1;
            const container = document.getElementById('po-items-container');
            const btnAdd = document.getElementById('btn-add-item');
            const grandTotalEl = document.getElementById('grand-total');

            const updateGrandTotal = () => {
                let total = 0;
                container.querySelectorAll('.po-item').forEach(row => {
                    const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.input-price').value) || 0;
                    const subtotal = qty * price;
                    row.querySelector('.input-subtotal').value = subtotal.toLocaleString('id-ID');
                    total += subtotal;
                });
                grandTotalEl.textContent = total.toLocaleString('id-ID');
            };

            const template = `
                <tr class="po-item">
                    <td>
                        <select name="items[__INDEX__][ingredient_id]" class="ingredient-select" required style="width:100%">
                            <option value="">— Pilih Bahan Baku —</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit->abbreviation }}">
                                    {{ $ingredient->name }} ({{ $ingredient->unit->abbreviation }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.5rem">
                            <input type="number" name="items[__INDEX__][quantity]" class="input-qty" step="any" min="0.0001" placeholder="0" required style="width:100%">
                            <span class="unit-label" style="font-size:0.875rem; color:var(--muted); min-width:30px">—</span>
                        </div>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][unit_price]" class="input-price" step="0.01" min="0" placeholder="0" required style="width:100%">
                    </td>
                    <td>
                        <input type="text" class="input-subtotal" readonly style="width:100%; background:var(--background); border-color:transparent; font-family:monospace" value="0">
                    </td>
                    <td style="text-align:center">
                        <button type="button" class="btn btn-sm btn-ghost remove-item" title="Hapus">
                            <i class="bi bi-x-lg" style="color:var(--danger)"></i>
                        </button>
                    </td>
                </tr>
            `;

            btnAdd.addEventListener('click', function() {
                const html = template.replace(/__INDEX__/g, itemIndex++);
                container.insertAdjacentHTML('beforeend', html);
                updateRemoveButtons();
            });

            container.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-item');
                if (btn && !btn.disabled) {
                    btn.closest('.po-item').remove();
                    updateRemoveButtons();
                    updateGrandTotal();
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('ingredient-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const unitLabel = e.target.closest('.po-item').querySelector('.unit-label');
                    unitLabel.textContent = (selectedOption && selectedOption.dataset.unit) ? selectedOption.dataset.unit : '—';
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('input-qty') || e.target.classList.contains('input-price')) {
                    updateGrandTotal();
                }
            });

            const updateRemoveButtons = () => {
                const rows = container.querySelectorAll('.po-item');
                const btns = container.querySelectorAll('.remove-item');
                btns.forEach(btn => btn.disabled = rows.length === 1);
            };
        });
    </script>
@endsection
