@extends('layouts.admin')

@section('title', 'Kategori Biaya Operasional – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>🏷️ Kategori Biaya Operasional</h1>
            <p>Kelola kategori untuk pengeluaran operasional.</p>
        </div>
        <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nama Kategori</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td style="color:var(--muted)">{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $category->name }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.expense-categories.edit', $category) }}" class="btn btn-sm btn-ghost" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.expense-categories.destroy', $category) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus kategori ini?');">
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
                            <td colspan="3" style="text-align:center; padding: 3rem; color:var(--muted)">
                                Belum ada kategori biaya operasional.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
