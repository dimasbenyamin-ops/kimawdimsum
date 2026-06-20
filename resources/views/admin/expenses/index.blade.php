@extends('layouts.admin')

@section('title', 'Biaya Operasional – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>💸 Biaya Operasional (OpEx)</h1>
            <p>Catat pengeluaran operasional seperti gaji, sewa, listrik, dll.</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Catat Biaya
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.expenses.index') }}" style="display:flex; gap:1rem; align-items:center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..." class="form-control" style="max-width:250px">
                
                <select name="expense_category_id" class="form-control" style="max-width:200px" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('expense_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1rem">Cari</button>
                @if(request('search') || request('expense_category_id'))
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-ghost" style="color:var(--danger)">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th style="text-align:right">Jumlah (Rp)</th>
                        <th>Pencatat</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $expense)
                        <tr>
                            <td style="color:var(--muted)">{{ $expenses->firstItem() + $index }}</td>
                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            <td><span class="badge" style="background:var(--surface-hover)">{{ $expense->category->name }}</span></td>
                            <td>{{ $expense->description ?? '-' }}</td>
                            <td style="text-align:right; font-family:monospace; color:var(--danger)">
                                {{ number_format($expense->amount, 2, ',', '.') }}
                            </td>
                            <td style="color:var(--muted)">{{ $expense->creator->name ?? 'System' }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-sm btn-ghost" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus catatan biaya ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost" title="Hapus">
                                        <i class="bi bi-trash3" style="color:var(--danger)"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 3rem; color:var(--muted)">
                                @if($search || $categoryId)
                                    Tidak ada data biaya yang cocok.
                                @else
                                    Belum ada catatan biaya operasional.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
@endsection
