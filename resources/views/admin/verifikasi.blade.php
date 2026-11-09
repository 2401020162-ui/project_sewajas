@extends('layouts.admin')

@section('judul', 'Verifikasi Pembayaran')
@section('subjudul', 'Periksa bukti pembayaran dan setujui pesanan customer.')

@section('isi')
<div class="chips-filter chips-admin">
    <a href="{{ route('admin.verifikasi.index') }}" class="{{ request('metode') ? '' : 'active' }}">Semua</a>
    @foreach(\App\Enums\MetodePembayaran::cases() as $metode)
        <a href="{{ route('admin.verifikasi.index', ['metode' => $metode->value]) }}"
           class="{{ request('metode') === $metode->value ? 'active' : '' }}">{{ $metode->label() }}</a>
    @endforeach
</div>

@forelse($pembayaran as $item)
    <article class="verify-card">
        <header class="verify-head">
            <div>
                <a class="table-link" href="{{ route('admin.pesanan.show', $item->pesanan) }}">{{ $item->pesanan->kode_pesanan }}</a>
                <small>Dikirim {{ $item->tanggal_bayar?->format('d M Y H:i') ?? '-' }}</small>
            </div>
            <div class="verify-head-right">
                <span class="badge badge-info">{{ $item->metode->label() }}</span>
                <strong>Rp {{ number_format($item->pesanan->total_harga, 0, ',', '.') }}</strong>
            </div>
        </header>

        <div class="verify-body">
            <div class="verify-customer">
                <dl class="detail-list">
                    <div><dt>Pelanggan</dt><dd>{{ $item->pesanan->user->name }}</dd></div>
                    <div><dt>No. HP</dt><dd>{{ $item->pesanan->user->nomor_hp ?? '-' }}</dd></div>
                    <div><dt>Jas</dt><dd>{{ $item->pesanan->jas->nama }} ({{ $item->pesanan->ukuran }}, {{ $item->pesanan->jumlah }} unit)</dd></div>
                    <div><dt>Jadwal</dt><dd>{{ $item->pesanan->tanggal_mulai->format('d M Y') }} – {{ $item->pesanan->tanggal_selesai->format('d M Y') }}</dd></div>
                </dl>
            </div>

            <div class="verify-proof">
                @if($item->bukti_url)
                    <a href="{{ $item->bukti_url }}" target="_blank" rel="noopener" class="proof-preview proof-preview-sm">
                        <img src="{{ $item->bukti_url }}" alt="Bukti pembayaran {{ $item->pesanan->kode_pesanan }}">
                        <span>Perbesar bukti</span>
                    </a>
                @else
                    <div class="empty-inline">
                        {{ $item->metode === \App\Enums\MetodePembayaran::Cash ? 'Pembayaran cash — konfirmasi setelah uang diterima.' : 'Bukti pembayaran belum diunggah.' }}
                    </div>
                @endif
            </div>
        </div>

        <footer class="verify-actions">
            <form method="POST" action="{{ route('admin.verifikasi.setujui', $item) }}" onsubmit="return confirm('Setujui pembayaran ini?')">
                @csrf
                <button class="btn btn-primary btn-sm" type="submit">Setujui Pembayaran</button>
            </form>

            <form class="reject-form" method="POST" action="{{ route('admin.verifikasi.tolak', $item) }}">
                @csrf
                <div class="field">
                    <label for="catatan-{{ $item->id }}">Alasan penolakan <span class="required">*</span></label>
                    <input id="catatan-{{ $item->id }}" name="catatan_admin" placeholder="Contoh: nominal tidak sesuai / bukti tidak terbaca" required>
                </div>
                <button class="btn btn-ghost btn-sm text-danger" type="submit">Tolak</button>
            </form>
        </footer>
    </article>
@empty
    <div class="empty-state">
        <span class="empty-icon" aria-hidden="true">&#10003;</span>
        <strong>Tidak ada pembayaran menunggu verifikasi</strong>
        <p>Semua pembayaran sudah diproses. Kerja bagus!</p>
    </div>
@endforelse

<div class="pagination-wrap">
    {{ $pembayaran->links('vendor.pagination.custom') }}
</div>
@endsection
