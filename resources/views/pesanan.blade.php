@extends('layouts.app')

@section('title', 'Pesanan Saya — PW_SEWAJAS')

@section('isi')
<section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <span>Pesanan Saya</span>
        </nav>
        <h1>Pesanan Saya</h1>
        <p>Pantau status pembayaran dan jadwal sewa jasmu di sini.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        @if($pesanan->count())
            <div class="order-list">
                @foreach($pesanan as $item)
                    <article class="order-card">
                        <header class="order-head">
                            <div>
                                <span class="order-code">{{ $item->kode_pesanan }}</span>
                                <small>Dipesan {{ $item->tanggal_pesan->format('d M Y') }}</small>
                            </div>
                            <span class="badge badge-{{ $item->badge_tampil }}">{{ $item->status_tampil }}</span>
                        </header>

                        <div class="order-body">
                            <div class="order-product">
                                <div class="order-thumb">
                                    @if($item->jas->gambar_url)
                                        <img src="{{ $item->jas->gambar_url }}" alt="{{ $item->jas->nama }}">
                                    @else
                                        <span>PW</span>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $item->jas->nama }}</strong>
                                    <small>Ukuran {{ $item->ukuran }} &middot; {{ $item->jumlah }} unit &middot; {{ $item->lama_sewa }} hari</small>
                                    <small>{{ $item->tanggal_mulai->format('d M Y') }} &ndash; {{ $item->tanggal_selesai->format('d M Y') }}</small>
                                </div>
                            </div>

                            <div class="order-payment">
                                <span>Total</span>
                                <strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong>
                                @if($item->pembayaran)
                                    <small class="badge badge-{{ $item->pembayaran->status->badge() }}">{{ $item->pembayaran->status->label() }}</small>
                                @else
                                    <small class="badge badge-muted">Belum dibayar</small>
                                @endif
                            </div>
                        </div>

                        <footer class="order-actions">
                            <a class="btn btn-outline btn-sm" href="{{ route('pesanan.show', $item) }}">Lihat Detail</a>

                            @if($item->status === \App\Enums\StatusPesanan::Pending)
                                @if(! $item->pembayaran || $item->pembayaran->status === \App\Enums\StatusPembayaran::Ditolak)
                                    <a class="btn btn-primary btn-sm" href="{{ route('pembayaran.show', $item) }}">Bayar Sekarang</a>
                                @else
                                    <a class="btn btn-ghost btn-sm" href="{{ route('pembayaran.show', $item) }}">Lihat Pembayaran</a>
                                @endif
                            @endif

                            @if($item->status->bisaDibatalkan())
                                <form method="POST" action="{{ route('pesanan.batal', $item) }}" onsubmit="return confirm('Batalkan pesanan {{ $item->kode_pesanan }}?')">
                                    @csrf
                                    <button class="btn btn-ghost btn-sm text-danger" type="submit">Batalkan</button>
                                </form>
                            @endif
                        </footer>
                    </article>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $pesanan->links('vendor.pagination.custom') }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon" aria-hidden="true">&#128203;</span>
                <strong>Belum ada pesanan</strong>
                <p>Setelah checkout, pesananmu akan muncul di halaman ini.</p>
                <a class="btn btn-primary" href="{{ route('katalog') }}">Mulai Sewa Jas</a>
            </div>
        @endif
    </div>
</section>
@endsection
