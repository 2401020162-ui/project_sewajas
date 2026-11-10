@extends('layouts.app')

@section('title', 'Keranjang — PW_SEWAJAS')

@section('isi')
<section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('katalog') }}">Katalog</a>
            <span>/</span>
            <span>Keranjang</span>
        </nav>
        <h1>Keranjang Sewa</h1>
        <p>Periksa kembali item, ukuran, dan tanggal sewa sebelum checkout.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        @if($items->isEmpty())
            <div class="empty-state">
                <span class="empty-icon" aria-hidden="true">&#128717;</span>
                <strong>Keranjangmu masih kosong</strong>
                <p>Yuk pilih jas yang cocok untuk acaramu terlebih dahulu.</p>
                <a class="btn btn-primary" href="{{ route('katalog') }}">Lihat Katalog</a>
            </div>
        @else
            <div class="cart-layout">
                <div class="cart-list">
                    @foreach($items as $item)
                        <article class="cart-item">
                            <a class="cart-thumb" href="{{ route('jas.detail', $item->jas) }}">
                                @if($item->jas->gambar_url)
                                    <img src="{{ $item->jas->gambar_url }}" alt="{{ $item->jas->nama }}">
                                @else
                                    <span class="cart-placeholder">PW</span>
                                @endif
                            </a>

                            <div class="cart-info">
                                <span class="badge badge-info">{{ $item->jas->kategori->nama }}</span>
                                <h3><a href="{{ route('jas.detail', $item->jas) }}">{{ $item->jas->nama }}</a></h3>
                                <p class="muted">
                                    Ukuran <strong>{{ $item->ukuran }}</strong> &middot;
                                    Rp {{ number_format($item->jas->harga_sewa, 0, ',', '.') }} / hari
                                </p>
                                <p class="cart-dates">
                                    {{ $item->tanggal_mulai->format('d M Y') }} &ndash; {{ $item->tanggal_selesai->format('d M Y') }}
                                    <span>({{ $item->lama_sewa }} hari)</span>
                                </p>

                                <div class="cart-item-actions">
                                    <form class="qty-form" method="POST" action="{{ route('keranjang.update', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="jumlah" value="{{ max(1, $item->jumlah - 1) }}" aria-label="Kurangi jumlah" @disabled($item->jumlah <= 1)>&minus;</button>
                                        <span aria-live="polite">{{ $item->jumlah }}</span>
                                        <button type="submit" name="jumlah" value="{{ min(10, $item->jumlah + 1) }}" aria-label="Tambah jumlah" @disabled($item->jumlah >= min(10, $item->jas->stok))>+</button>
                                    </form>

                                    <form method="POST" action="{{ route('keranjang.destroy', $item) }}" onsubmit="return confirm('Hapus {{ $item->jas->nama }} dari keranjang?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </div>

                            <div class="cart-subtotal">
                                <span>Subtotal</span>
                                <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        </article>
                    @endforeach

                    <div class="cart-list-footer">
                        <a class="text-link" href="{{ route('katalog') }}">&larr; Lanjut pilih jas</a>
                        <form method="POST" action="{{ route('keranjang.kosongkan') }}" onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button class="link-danger" type="submit">Kosongkan keranjang</button>
                        </form>
                    </div>
                </div>

                <aside class="cart-summary">
                    <h2>Ringkasan Pesanan</h2>
                    <dl>
                        <div><dt>Jumlah item</dt><dd>{{ $items->sum('jumlah') }} jas</dd></div>
                        <div><dt>Total sewa</dt><dd>Rp {{ number_format($total, 0, ',', '.') }}</dd></div>
                        <div><dt>Deposit jaminan</dt><dd>Rp 100.000</dd></div>
                    </dl>
                    <p class="summary-note">
                        Harga dihitung otomatis berdasarkan durasi sewa dan jumlah jas. Setiap item akan menjadi satu pesanan.
                        Deposit jaminan dibayar di toko dan dikembalikan setelah jas kembali dalam kondisi baik (tidak termasuk total sewa).
                    </p>

                    <form method="POST" action="{{ route('checkout') }}" class="form">
                        @csrf
                        <div class="field">
                            <label for="nama_pemesan">Nama pemesan</label>
                            <input id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', auth()->user()->name) }}" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label for="nomor_hp">Nomor HP</label>
                            <input id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', auth()->user()->nomor_hp) }}" inputmode="numeric" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="field">
                            <label for="catatan">Catatan untuk admin <span class="optional">(opsional)</span></label>
                            <textarea id="catatan" name="catatan" rows="3" maxlength="500" placeholder="Contoh: ambil jas jam 14.00">{{ old('catatan') }}</textarea>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg" type="submit">Kirim Pesanan</button>
                    </form>

                    <div class="summary-contact">
                        <strong>Butuh bantuan?</strong>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener">Chat admin via WhatsApp</a>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>
@endsection
