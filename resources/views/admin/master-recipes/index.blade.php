@extends('layouts.admin')

@section('title', 'Master Resep – Kumaw Dimsum Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>📖 Master Resep (BOM)</h1>
            <p>Kelola resep dasar atau produk setengah jadi (contoh: "Dimsum Original Per Biji").</p>
        </div>
        <a href="{{ route('admin.master-recipes.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg"></i> Tambah Master Resep
        </a>
    </div>

    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border)">
            <form method="GET" action="{{ route('admin.master-recipes.index') }}" style="max-width:300px">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari master resep..." 
                       class="form-control" onchange="this.form.submit()">
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nama Master Resep</th>
                        <th>Deskripsi</th>
                        <th>Jml Bahan Baku</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterRecipes as $index => $mr)
                        <tr>
                            <td style="color:var(--muted)">{{ $masterRecipes->firstItem() + $index }}</td>
                            <td>
                                <div style="font-weight: 500; color:var(--text)">{{ $mr->name }}</div>
                            </td>
                            <td style="color:var(--muted); font-size:0.9rem">
                                {{ $mr->description ?: '-' }}
                            </td>
                            <td>
                                <span class="badge" style="background:var(--surface-hover); color:var(--text)">
                                    {{ $mr->ingredients_count }} Bahan
                                </span>
                            </td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.master-recipes.edit', $mr) }}" class="btn btn-sm btn-ghost" title="Edit Master Resep">
                                    <i class="bi bi-pencil"></i> Edit Resep
                                </a>
                                <form action="{{ route('admin.master-recipes.destroy', $mr) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus master resep ini? Pastikan tidak sedang digunakan oleh menu.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 3rem; color:var(--muted)">
                                @if($search)
                                    Tidak ada master resep yang cocok dengan pencarian "{{ $search }}".
                                @else
                                    Belum ada data master resep.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($masterRecipes->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--border)">
                {{ $masterRecipes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
