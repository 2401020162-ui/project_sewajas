@extends('layouts.app')

@section('title', 'Pembayaran ' . $pesanan->kode_pesanan . ' — PW_SEWAJAS')

@section('isi')
@php
    $bayar = $pesanan->pembayaran;
    $bisaBayar = $pesanan->status === \App\Enums\StatusPesanan::Pending
        && (! $bayar || $bayar->status === \App\Enums\StatusPembayaran::Ditolak);
@endphp

<section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('pesanan.index') }}">Pesanan Saya</a>
            <span>/</span>
            <span>Pembayaran</span>
        </nav>
        <h1>Pembayaran</h1>
        <p>{{ $pesanan->kode_pesanan }} &middot; Total Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
    </div>
</section>

<section class="section">
    <div class="container payment-layout">
        <div class="payment-main">
            @if($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                <div class="alert alert-danger">
                    <span class="alert-icon">!</span>
                    <div>Pesanan ini sudah dibatalkan sehingga tidak dapat dibayar.</div>
                </div>
            @elseif($bayar && $bayar->status === \App\Enums\StatusPembayaran::Diverifikasi)
                <div class="panel">
                    <div class="payment-success">
                        <span class="payment-success-icon">&#10003;</span>
                        <h2>Pembayaran Lunas</h2>
                        <p>Pembayaranmu sudah diverifikasi admin. Silakan ambil jas sesuai jadwal yang tertera.</p>
                        <a class="btn btn-primary" href="{{ route('pesanan.show', $pesanan) }}">Lihat Detail Pesanan</a>
                    </div>
                </div>
            @elseif($bayar && $bayar->status === \App\Enums\StatusPembayaran::Pending)
                <div class="panel">
                    <div class="payment-waiting">
                        <span class="payment-waiting-icon">&#9201;</span>
                        <h2>Menunggu Verifikasi</h2>
                        <p>Bukti pembayaranmu sudah diterima. Admin akan memverifikasi dalam 1&times;24 jam kerja.</p>
                        <dl class="detail-list">
                            <div><dt>Metode</dt><dd>{{ $bayar->metode->label() }}</dd></div>
                            <div><dt>Tanggal bayar</dt><dd>{{ $bayar->tanggal_bayar?->format('d M Y H:i') ?? '-' }}</dd></div>
                            <div><dt>Status</dt><dd><span class="badge badge-warning">{{ $bayar->status->label() }}</span></dd></div>
                        </dl>
                        @if($bayar->bukti_url)
                            <a class="proof-preview" href="{{ $bayar->bukti_url }}" target="_blank" rel="noopener">
                                <img src="{{ $bayar->bukti_url }}" alt="Bukti pembayaran">
                                <span>Lihat bukti pembayaran</span>
                            </a>
                        @endif
                        <a class="btn btn-outline" href="{{ route('pesanan.show', $pesanan) }}">Kembali ke Detail Pesanan</a>
                    </div>
                </div>
            @else
                @if($bayar && $bayar->status === \App\Enums\StatusPembayaran::Ditolak)
                    <div class="alert alert-danger">
                        <span class="alert-icon">!</span>
                        <div>
                            <strong>Pembayaran sebelumnya ditolak.</strong>
                            <p>{{ $bayar->catatan_admin ?: 'Silakan unggah ulang bukti pembayaran yang benar.' }}</p>
                        </div>
                    </div>
                @endif

                <div class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Pilih Metode Pembayaran</h2>
                            <p class="muted">Bayar dengan QRIS atau tunai langsung di toko.</p>
                        </div>
                        <span class="badge badge-warning">{{ $pesanan->status_tampil }}</span>
                    </div>

                    <div class="tabs payment-tabs" role="tablist" aria-label="Metode pembayaran">
                        <button type="button" role="tab" data-tab="qris" class="active" aria-selected="true">QRIS</button>
                        <button type="button" role="tab" data-tab="cash" aria-selected="false">Cash di Toko</button>
                    </div>

                    <div class="tab-panel active" id="tab-qris" role="tabpanel">
                        <div class="qris-card">
                            <img class="qris-image" src="{{ asset('images/qris.svg') }}" alt="Kode QRIS PW_SEWAJAS">
                            <div>
                                <h3>Scan &amp; bayar</h3>
                                <p class="muted">Scan kode QRIS di samping menggunakan aplikasi bank atau e-wallet, lalu unggah bukti pembayaran.</p>
                                <p class="muted">Nominal: <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('pembayaran.store', $pesanan) }}" enctype="multipart/form-data" class="form">
                            @csrf
                            <input type="hidden" name="metode" value="qris">
                            <div class="field">
                                <label for="bukti-qris">Bukti pembayaran QRIS</label>
                                <input id="bukti-qris" type="file" name="bukti_pembayaran" accept="image/*" required>
                                <small class="field-help">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">Kirim Bukti QRIS</button>
                        </form>
                    </div>

                    <div class="tab-panel" id="tab-cash" role="tabpanel">
                        <div class="cash-info">
                            <h3>Bayar tunai di toko</h3>
                            <p>Datang ke toko PW_SEWAJAS dan tunjukkan kode pesanan <strong>{{ $pesanan->kode_pesanan }}</strong> ke admin.</p>
                            <ul class="check-list">
                                <li>Alamat: Jl. Merdeka No. 21, Tanjungpinang</li>
                                <li>Jam buka: Senin–Sabtu, 09.00–20.00 WIB</li>
                                <li>Nominal: <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('pembayaran.store', $pesanan) }}" class="form">
                            @csrf
                            <input type="hidden" name="metode" value="cash">
                            <button class="btn btn-primary btn-block" type="submit">Konfirmasi Bayar di Toko</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <aside class="order-summary-panel">
            <h2>Detail Tagihan</h2>
            <div class="order-product">
                <div class="order-thumb">
                    @if($pesanan->jas->gambar_url)
                        <img src="{{ $pesanan->jas->gambar_url }}" alt="{{ $pesanan->jas->nama }}">
                    @else
                        <span>PW</span>
                    @endif
                </div>
                <div>
                    <strong>{{ $pesanan->jas->nama }}</strong>
                    <small>Ukuran {{ $pesanan->ukuran }} &middot; {{ $pesanan->jumlah }} unit</small>
                </div>
            </div>
            <dl class="summary-list">
                <div><dt>Durasi</dt><dd>{{ $pesanan->lama_sewa }} hari</dd></div>
                <div><dt>Mulai</dt><dd>{{ $pesanan->tanggal_mulai->format('d M Y') }}</dd></div>
                <div><dt>Selesai</dt><dd>{{ $pesanan->tanggal_selesai->format('d M Y') }}</dd></div>
                <div class="summary-total"><dt>Total</dt><dd>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</dd></div>
            </dl>
            <a class="btn btn-ghost btn-block" href="{{ route('pesanan.show', $pesanan) }}">Lihat Detail Pesanan</a>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (() => {
        const tabs = document.querySelectorAll('.payment-tabs [data-tab]');
        const panels = document.querySelectorAll('.payment-main .tab-panel');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((item) => {
                    const active = item === tab;
                    item.classList.toggle('active', active);
                    item.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                panels.forEach((panel) => panel.classList.toggle('active', panel.id === 'tab-' + tab.dataset.tab));
            });
        });
    })();
</script>
@endpush
