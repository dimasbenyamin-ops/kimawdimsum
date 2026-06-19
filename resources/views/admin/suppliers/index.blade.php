@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')
<div class="page-header">
    <div>
        <h1>🚚 Supplier</h1>
        <p>Kelola data supplier / vendor bahan baku</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg"></i> Tambah Supplier
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
@endif

{{-- Search --}}
<div style="margin-bottom:1.5rem">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" style="max-width:360px;position:relative">
        <i class="bi bi-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.875rem"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari supplier..." maxlength="100"
               style="padding-left:2.25rem;width:100%">
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>PIC</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td><strong>{{ e($supplier->name) }}</strong></td>
                        <td style="color:var(--muted)">{{ e($supplier->contact_person ?? '—') }}</td>
                        <td>{{ e($supplier->phone ?? '—') }}</td>
                        <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ e($supplier->address ?? '') }}">
                            {{ e($supplier->address ?? '—') }}
                        </td>
                        <td>
                            @if($supplier->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-muted">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:0.375rem;justify-content:center">
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-ghost btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                      onsubmit="return confirm('Hapus supplier &quot;{{ e($supplier->name) }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">
                            <div style="font-size:2rem;margin-bottom:0.5rem">🚚</div>
                            Belum ada supplier.
                            <a href="{{ route('admin.suppliers.create') }}" style="color:var(--gold)">Tambah sekarang →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($suppliers->hasPages())
    <div style="margin-top:1.25rem">
        {{ $suppliers->links() }}
    </div>
@endif
@endsection
