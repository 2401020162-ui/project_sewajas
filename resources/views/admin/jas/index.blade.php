@extends('layouts.admin')

@section('judul', 'Kelola Jas')
@section('subjudul', 'Tambah, ubah, dan atur ketersediaan koleksi jas.')

@section('isi')
<div class="toolbar">
    <form class="toolbar-filters" method="GET" action="{{ route('admin.jas.index') }}">
        <div class="field">
            <label class="sr-only" for="cari">Cari jas</label>
            <input id="cari" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama jas...">
        </div>
        <div class="field">
            <label class="sr-only" for="kategori">Kategori</label>
            <select id="kategori" name="kategori">
                <option value="">Semua kategori</option>
                @foreach($kategori as $item)
                    <option value="{{ $item->id }}" @selected(request('kategori') == $item->id)>{{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="sr-only" for="status">Status</label>
            <select id="status" name="status">
                <option value="">Semua status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <button class="btn btn-outline btn-sm" type="submit">Filter</button>
        @if(request()->hasAny(['q', 'kategori', 'status']))
            <a class="text-link" href="{{ route('admin.jas.index') }}">Reset</a>
        @endif
    </form>

    <a class="btn btn-primary" href="{{ route('admin.jas.create') }}">+ Tambah Jas</a>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Jas</th>
                    <th>Kategori</th>
                    <th>Ukuran</th>
                    <th>Harga / hari</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jas as $item)
                    <tr>
                        <td>
                            <div class="table-thumb">
                                @if($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}">
                                @else
                                    <span>PW</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            <small class="table-sub">{{ Str::limit($item->deskripsi, 60) ?: 'Tanpa deskripsi' }}</small>
                        </td>
                        <td>{{ $item->kategori->nama }}</td>
                        <td>{{ $item->ukuran }}</td>
                        <td>Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $item->stok < 1 ? 'badge-danger' : ($item->stok <= 2 ? 'badge-warning' : 'badge-success') }}">{{ $item->stok }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $item->is_aktif ? 'badge-success' : 'badge-muted' }}">{{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-outline btn-xs" href="{{ route('admin.jas.edit', $item) }}">Ubah</a>
                                <form method="POST" action="{{ route('admin.jas.destroy', $item) }}" onsubmit="return confirm('Hapus jas {{ $item->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-xs text-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Belum ada data jas. Klik “Tambah Jas” untuk memulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $jas->links('vendor.pagination.custom') }}
    </div>
</section>
@endsection
