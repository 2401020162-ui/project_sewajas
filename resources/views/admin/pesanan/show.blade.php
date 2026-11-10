@extends('layouts.admin')

@section('judul', 'Detail Pesanan')
@section('subjudul', $pesanan->kode_pesanan . ' · ' . $pesanan->user->name)

@section('isi')
@php
    $urutan = ['pending' => 0, 'menunggu_verifikasi' => 1, 'disewa' => 2, 'selesai' => 3];
    $posisi = $urutan[$pesanan->status->value] ?? 0;
    $dibatalkan = $pesanan->status === \App\Enums\StatusPesanan::Dibatalkan;
@endphp

<div class="toolbar">
    <a class="btn btn-outline btn-sm" href="{{ route('admin.pesanan.index') }}">&larr; Kembali</a>
    <div class="table-actions">
        @if($pesanan->status->bisaDibatalkan())
            <form method="POST" action="{{ route('admin.pesanan.batal', $pesanan) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                @csrf
                <button class="btn btn-ghost btn-sm text-danger" type="submit">Batalkan</button>
            </form>
        @endif
    </div>
</div>

<div class="admin-grid-2">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Informasi Pesanan</h2>
                <p class="muted">Dibuat {{ $pesanan->tanggal_pesan->format('d M Y') }}.</p>
            </div>
            <span class="badge badge-{{ $pesanan->badge_tampil }}">{{ $pesanan->status_tampil }}</span>
        </div>

        <dl class="detail-list">
            <div><dt>Kode pesanan</dt><dd>{{ $pesanan->kode_pesanan }}</dd></div>
            <div><dt>Pelanggan</dt><dd>{{ $pesanan->nama_pemesan_tampil }}</dd></div>
            <div><dt>Nomor HP</dt><dd>{{ $pesanan->nomor_hp_tampil }}</dd></div>
            <div><dt>Jas</dt><dd>{{ $pesanan->jas->nama }} ({{ $pesanan->ukuran }}, {{ $pesanan->jumlah }} unit)</dd></div>
            <div><dt>Kategori</dt><dd>{{ $pesanan->jas->kategori->nama }}</dd></div>
            <div><dt>Jadwal</dt><dd>{{ $pesanan->tanggal_mulai->format('d M Y') }} – {{ $pesanan->tanggal_selesai->format('d M Y') }} ({{ $pesanan->lama_sewa }} hari)</dd></div>
            @if($pesanan->tanggal_dikembalikan)
                <div><dt>Dikembalikan</dt><dd>{{ $pesanan->tanggal_dikembalikan->format('d M Y') }}</dd></div>
            @endif
            <div><dt>Total</dt><dd><strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></dd></div>
            @if($pesanan->catatan)
                <div><dt>Catatan</dt><dd>{{ $pesanan->catatan }}</dd></div>
            @endif
            @if($pesanan->catatan_pengembalian)
                <div><dt>Kondisi kembali</dt><dd>{{ $pesanan->catatan_pengembalian }}</dd></div>
            @endif
        </dl>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Pembayaran</h2>
                <p class="muted">Status verifikasi pembayaran customer.</p>
            </div>
        </div>

        @if($pesanan->pembayaran)
            <dl class="detail-list">
                <div><dt>Metode</dt><dd>{{ $pesanan->pembayaran->metode->label() }}</dd></div>
                <div><dt>Status</dt><dd><span class="badge badge-{{ $pesanan->pembayaran->status->badge() }}">{{ $pesanan->pembayaran->status->label() }}</span></dd></div>
                <div>
                    <dt>{{ $pesanan->pembayaran->metode === \App\Enums\MetodePembayaran::Cash ? 'Dikonfirmasi' : 'Tanggal bayar' }}</dt>
                    <dd>{{ $pesanan->pembayaran->tanggal_bayar?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div><dt>Diverifikasi oleh</dt><dd>{{ $pesanan->pembayaran->verifikator->name ?? '-' }}</dd></div>
                @if($pesanan->pembayaran->catatan_admin)
                    <div><dt>Catatan admin</dt><dd>{{ $pesanan->pembayaran->catatan_admin }}</dd></div>
                @endif
            </dl>

            @if($pesanan->pembayaran->bukti_url)
                <a class="proof-preview" href="{{ $pesanan->pembayaran->bukti_url }}" target="_blank" rel="noopener">
                    <img src="{{ $pesanan->pembayaran->bukti_url }}" alt="Bukti pembayaran">
                    <span>Buka bukti pembayaran</span>
                </a>
            @else
                <div class="empty-inline">Tidak ada file bukti pembayaran (metode cash atau belum diunggah).</div>
            @endif
        @else
            <div class="empty-inline">Customer belum memilih metode pembayaran.</div>
        @endif
    </section>
</div>

@if($pesanan->status === \App\Enums\StatusPesanan::Disewa)
    <section class="panel" id="pengembalian">
        <div class="panel-head">
            <div>
                <h2>Tandai Jas Dikembalikan</h2>
                <p class="muted">Catat kondisi jas saat kembali. Stok akan otomatis ditambahkan lagi.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pesanan.selesai', $pesanan) }}" class="form">
            @csrf
            <div class="field">
                <label for="catatan_pengembalian">Catatan kondisi pengembalian <span class="optional">(opsional)</span></label>
                <textarea id="catatan_pengembalian" name="catatan_pengembalian" rows="3" maxlength="500" placeholder="Contoh: jas kembali bersih dan lengkap / ada noda di bagian kerah">{{ old('catatan_pengembalian') }}</textarea>
                <small class="field-help">Isi jika ada kerusakan, noda, atau bagian jas yang kurang.</small>
            </div>
            <button class="btn btn-primary" type="submit" onclick="return confirm('Tandai jas sudah dikembalikan dan selesaikan pesanan?')">Tandai Dikembalikan</button>
        </form>
    </section>
@endif

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Riwayat Status</h2>
            <p class="muted">Alur pesanan dari dibuat sampai selesai.</p>
        </div>
    </div>

    @if($dibatalkan)
        <div class="alert alert-danger">
            <span class="alert-icon">!</span>
            <div>Pesanan ini dibatalkan. Stok jas sudah dikembalikan.</div>
        </div>
    @else
        <ol class="timeline timeline-horizontal">
            @foreach([['Pesanan dibuat', \App\Enums\StatusPesanan::Pending], ['Menunggu verifikasi', \App\Enums\StatusPesanan::MenungguVerifikasi], ['Disewa', \App\Enums\StatusPesanan::Disewa], ['Selesai', \App\Enums\StatusPesanan::Selesai]] as $index => $step)
                <li class="{{ $index <= $posisi ? 'done' : '' }} {{ $index === $posisi ? 'current' : '' }}">
                    <span class="timeline-dot">{{ $index + 1 }}</span>
                    <strong>{{ $step[0] }}</strong>
                </li>
            @endforeach
        </ol>
    @endif
</section>
@endsection
