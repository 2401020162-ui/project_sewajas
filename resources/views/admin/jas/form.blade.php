@extends('layouts.admin')

@section('judul', $jas->exists ? 'Ubah Jas' : 'Tambah Jas')
@section('subjudul', $jas->exists ? 'Perbarui informasi jas yang sudah ada.' : 'Lengkapi informasi jas baru untuk katalog.')

@section('isi')
<form class="panel form" method="POST" enctype="multipart/form-data"
      action="{{ $jas->exists ? route('admin.jas.update', $jas) : route('admin.jas.store') }}">
    @csrf
    @if($jas->exists)
        @method('PUT')
    @endif

    <div class="form-grid">
        <div class="form-main">
            <div class="field">
                <label for="nama">Nama jas <span class="required">*</span></label>
                <input id="nama" name="nama" value="{{ old('nama', $jas->nama) }}" maxlength="120" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="kategori_id">Kategori <span class="required">*</span></label>
                    <select id="kategori_id" name="kategori_id" required>
                        <option value="">Pilih kategori</option>
                        @foreach($kategori as $item)
                            <option value="{{ $item->id }}" @selected(old('kategori_id', $jas->kategori_id) == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="ukuran">Ukuran <span class="required">*</span></label>
                    <input id="ukuran" name="ukuran" value="{{ old('ukuran', $jas->ukuran ?? 'S,M,L,XL') }}" placeholder="S,M,L,XL" required>
                    <small class="field-help">Pisahkan dengan koma.</small>
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="harga_sewa">Harga sewa per hari <span class="required">*</span></label>
                    <input id="harga_sewa" type="number" name="harga_sewa" value="{{ old('harga_sewa', $jas->harga_sewa) }}" min="1000" step="500" required>
                </div>
                <div class="field">
                    <label for="stok">Stok <span class="required">*</span></label>
                    <input id="stok" type="number" name="stok" value="{{ old('stok', $jas->stok ?? 0) }}" min="0" required>
                </div>
            </div>

            <div class="field">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="2000" placeholder="Jelaskan bahan, potongan, dan cocok untuk acara apa...">{{ old('deskripsi', $jas->deskripsi) }}</textarea>
            </div>

            <label class="checkbox">
                <input type="hidden" name="is_aktif" value="0">
                <input type="checkbox" name="is_aktif" value="1" @checked(old('is_aktif', $jas->exists ? $jas->is_aktif : true))>
                <span>Tampilkan jas di katalog</span>
            </label>
        </div>

        <aside class="form-side">
            <div class="field">
                <label for="gambar">Foto jas</label>
                <div class="image-preview" id="imagePreview">
                    @if($jas->gambar_url)
                        <img src="{{ $jas->gambar_url }}" alt="{{ $jas->nama }}">
                    @else
                        <span>Belum ada foto</span>
                    @endif
                </div>
                <input id="gambar" type="file" name="gambar" accept="image/*">
                <small class="field-help">JPG, PNG, atau WEBP. Maksimal 2 MB. Rasio ideal 1:1.</small>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary btn-block" type="submit">{{ $jas->exists ? 'Simpan Perubahan' : 'Tambah Jas' }}</button>
                <a class="btn btn-ghost btn-block" href="{{ route('admin.jas.index') }}">Batal</a>
            </div>
        </aside>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('gambar');
        const preview = document.getElementById('imagePreview');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);
            preview.innerHTML = '<img src="' + url + '" alt="Pratinjau foto">';
        });
    })();
</script>
@endpush
