@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $pesanan->kode_pesanan . ' — PW_SEWAJAS')

@section('isi')
@php
    $langkah = [
        ['label' => 'Pesanan dibuat', 'status' => \App\Enums\StatusPesanan::Pending],
        ['label' => 'Pembayaran disetujui', 'status' => \App\Enums\StatusPesanan::Disetujui],
        ['label' => 'Sedang disewa', 'status' => \App\Enums\StatusPesanan::Dikirim],
        ['label' => 'Selesai', 'status' => \App\Enums\StatusPesanan::Selesai],
    ];
    $urutan = ['pending' => 0, 'disetujui' => 1, 'dikirim' => 2, 'selesai' => 3];
    $posisi = $urutan[$pesanan->status->value] ?? 0;
    $dibatalkan = $pesanan->status === \App\Enums\StatusPesanan::Dibatalkan;
@endphp

<section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('pesanan.index') }}">Pesanan Saya</a>
            <span>/</span>
            <span>{{ $pesanan->kode_pesanan }}</span>
        </nav>
        <div class="page-title-row">
            <div>
                <h1>Detail Pesanan</h1>
                <p>{{ $pesanan->kode_pesanan }} &middot; {{ $pesanan->tanggal_pesan->format('d M Y') }}</p>
            </div>
            <span class="badge badge-{{ $pesanan->badge_tampil }} badge-lg">{{ $pesanan->status_tampil }}</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container order-detail-layout">
        <div class="order-detail-main">
            @if($dibatalkan)
                <div class="alert alert-danger">
                    <span class="alert-icon">!</span>
                    <div>Pesanan ini telah dibatalkan. Stok jas sudah dikembalikan ke katalog.</div>
                </div>
            @else
                <div class="panel">
                    <h2>Status Pesanan</h2>
                    <ol class="timeline">
                        @foreach($langkah as $index => $step)
                            <li class="{{ $index <= $posisi ? 'done' : '' }} {{ $index === $posisi ? 'current' : '' }}">
                                <span class="timeline-dot">{{ $index + 1 }}</span>
                                <div>
                                    <strong>{{ $step['label'] }}</strong>
                                    <small>
                                        @if($index === 0)
                                            {{ $pesanan->tanggal_pesan->format('d M Y') }}
                                        @elseif($index === 1 && $pesanan->pembayaran?->tanggal_verifikasi)
                                            {{ $pesanan->pembayaran->tanggal_verifikasi->format('d M Y H:i') }}
                                        @elseif($index === 3 && $pesanan->tanggal_dikembalikan)
                                            {{ $pesanan->tanggal_dikembalikan->format('d M Y') }}
                                        @else
                                            Menunggu proses
                                        @endif
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            <div class="panel">
                <h2>Informasi Jas</h2>
                <div class="order-product order-product-lg">
                    <div class="order-thumb">
                        @if($pesanan->jas->gambar_url)
                            <img src="{{ $pesanan->jas->gambar_url }}" alt="{{ $pesanan->jas->nama }}">
                        @else
                            <span>PW</span>
                        @endif
                    </div>
                    <div>
                        <span class="badge badge-info">{{ $pesanan->jas->kategori->nama }}</span>
                        <h3><a href="{{ route('jas.detail', $pesanan->jas) }}">{{ $pesanan->jas->nama }}</a></h3>
                        <p class="muted">Ukuran {{ $pesanan->ukuran }} &middot; {{ $pesanan->jumlah }} unit &middot; {{ $pesanan->lama_sewa }} hari</p>
                        <p>{{ $pesanan->tanggal_mulai->format('d M Y') }} &ndash; {{ $pesanan->tanggal_selesai->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2>Pembayaran</h2>
                @if($pesanan->pembayaran)
                    <dl class="detail-list">
                        <div><dt>Metode</dt><dd>{{ $pesanan->pembayaran->metode->label() }}</dd></div>
                        <div><dt>Status</dt><dd><span class="badge badge-{{ $pesanan->pembayaran->status->badge() }}">{{ $pesanan->pembayaran->status->label() }}</span></dd></div>
                        <div><dt>Tanggal bayar</dt><dd>{{ $pesanan->pembayaran->tanggal_bayar?->format('d M Y H:i') ?? '-' }}</dd></div>
                        @if($pesanan->pembayaran->tanggal_verifikasi)
                            <div><dt>Diverifikasi</dt><dd>{{ $pesanan->pembayaran->tanggal_verifikasi->format('d M Y H:i') }}</dd></div>
                        @endif
                    </dl>

                    @if($pesanan->pembayaran->bukti_url)
                        <a class="proof-preview" href="{{ $pesanan->pembayaran->bukti_url }}" target="_blank" rel="noopener">
                            <img src="{{ $pesanan->pembayaran->bukti_url }}" alt="Bukti pembayaran {{ $pesanan->kode_pesanan }}">
                            <span>Lihat bukti pembayaran</span>
                        </a>
                    @endif

                    @if($pesanan->pembayaran->catatan_admin)
                        <div class="alert alert-warning">
                            <span class="alert-icon">!</span>
                            <div><strong>Catatan admin:</strong> {{ $pesanan->pembayaran->catatan_admin }}</div>
                        </div>
                    @endif
                @else
                    <p class="muted">Belum ada pembayaran. Silakan pilih metode pembayaran untuk melanjutkan.</p>
                @endif
            </div>

            @if($pesanan->catatan)
                <div class="panel">
                    <h2>Catatan</h2>
                    <p>{{ $pesanan->catatan }}</p>
                </div>
            @endif
        </div>

        <aside class="order-summary-panel">
            <h2>Ringkasan</h2>
            <dl class="summary-list">
                <div><dt>Kode pesanan</dt><dd>{{ $pesanan->kode_pesanan }}</dd></div>
                <div><dt>Customer</dt><dd>{{ $pesanan->user->name }}</dd></div>
                <div><dt>No. HP</dt><dd>{{ $pesanan->user->nomor_hp ?? '-' }}</dd></div>
                <div><dt>Durasi</dt><dd>{{ $pesanan->lama_sewa }} hari</dd></div>
                <div class="summary-total"><dt>Total</dt><dd>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</dd></div>
            </dl>

            @if($pesanan->status === \App\Enums\StatusPesanan::Pending)
                @if(! $pesanan->pembayaran || $pesanan->pembayaran->status === \App\Enums\StatusPembayaran::Ditolak)
                    <a class="btn btn-primary btn-block" href="{{ route('pembayaran.show', $pesanan) }}">Bayar Sekarang</a>
                @else
                    <a class="btn btn-outline btn-block" href="{{ route('pembayaran.show', $pesanan) }}">Lihat Instruksi Pembayaran</a>
                @endif
            @endif

            @if($pesanan->status === \App\Enums\StatusPesanan::Selesai)
                <a class="btn btn-primary btn-block" href="{{ route('jas.detail', $pesanan->jas) }}#ulasan">Tulis Testimoni</a>
            @endif

            @if($pesanan->status->bisaDibatalkan())
                <form method="POST" action="{{ route('pesanan.batal', $pesanan) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button class="btn btn-ghost btn-block text-danger" type="submit">Batalkan Pesanan</button>
                </form>
            @endif

            <a class="btn btn-ghost btn-block" href="{{ route('pesanan.index') }}">&larr; Kembali ke daftar</a>
        </aside>
    </div>
</section>
@endsection
