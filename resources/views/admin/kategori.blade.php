@extends('layouts.admin')

@section('judul', 'Kelola Kategori')
@section('subjudul', 'Atur pengelompokan koleksi jas agar katalog lebih mudah ditelusuri.')

@section('isi')
<div class="admin-grid-sidebar">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Daftar Kategori</h2>
                <p class="muted">{{ $kategori->count() }} kategori terdaftar.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Jumlah Jas</th>
                        <th class="th-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td><code>{{ $item->slug }}</code></td>
                            <td><span class="badge badge-info">{{ $item->jas_count }} jas</span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-outline btn-xs" href="{{ route('admin.kategori.index', ['edit' => $item->id]) }}">Ubah</a>
                                    <form method="POST" action="{{ route('admin.kategori.destroy', $item) }}" onsubmit="return confirm('Hapus kategori {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-ghost btn-xs text-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="table-empty">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <aside class="panel">
        <div class="panel-head">
            <div>
                <h2>{{ $edit ? 'Ubah Kategori' : 'Tambah Kategori' }}</h2>
                <p class="muted">{{ $edit ? 'Perbarui nama kategori.' : 'Buat kategori baru untuk katalog.' }}</p>
            </div>
        </div>

        <form class="form" method="POST" action="{{ $edit ? route('admin.kategori.update', $edit) : route('admin.kategori.store') }}">
            @csrf
            @if($edit)
                @method('PUT')
            @endif

            <div class="field">
                <label for="nama">Nama kategori <span class="required">*</span></label>
                <input id="nama" name="nama" value="{{ old('nama', $edit->nama ?? '') }}" maxlength="100" placeholder="Contoh: Jas Pengantin" required>
                <small class="field-help">Slug akan dibuat otomatis.</small>
            </div>

            <button class="btn btn-primary btn-block" type="submit">{{ $edit ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
            @if($edit)
                <a class="btn btn-ghost btn-block" href="{{ route('admin.kategori.index') }}">Batal</a>
            @endif
        </form>
    </aside>
</div>
@endsection
