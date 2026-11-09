@extends('layouts.admin')

@section('judul', 'Laporan Transaksi')
@section('subjudul', 'Rekap transaksi dan pendapatan berdasarkan rentang tanggal.')

@section('isi')
<div class="toolbar no-print">
    <form class="toolbar-filters" method="GET" action="{{ route('admin.laporan') }}">
        <div class="field">
            <label for="tanggal_awal">Tanggal awal</label>
            <input id="tanggal_awal" type="date" name="tanggal_awal" value="{{ request('tanggal_awal', $awal->toDateString()) }}" required>
        </div>
        <div class="field">
            <label for="tanggal_akhir">Tanggal akhir</label>
            <input id="tanggal_akhir" type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', $akhir->toDateString()) }}" required>
        </div>
        <button class="btn btn-primary btn-sm" type="submit">Tampilkan</button>
    </form>

    <button class="btn btn-outline btn-sm" type="button" onclick="window.print()">Cetak Laporan</button>
</div>

<div class="print-header">
    <h1>PW_SEWAJAS</h1>
    <p>Laporan Transaksi &middot; {{ $awal->format('d M Y') }} – {{ $akhir->format('d M Y') }}</p>
</div>

<div class="stat-grid stat-grid-3">
    <article class="stat-card">
        <span class="stat-icon">&#128203;</span>
        <div>
            <span class="stat-label">Jumlah transaksi</span>
            <strong class="stat-value">{{ number_format($data->count(), 0, ',', '.') }}</strong>
            <small>Pesanan pada periode ini</small>
        </div>
    </article>
    <article class="stat-card stat-card-gold">
        <span class="stat-icon">&#128176;</span>
        <div>
            <span class="stat-label">Pendapatan terverifikasi</span>
            <strong class="stat-value stat-value-sm">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
            <small>Pembayaran berstatus lunas</small>
        </div>
    </article>
    <article class="stat-card stat-card-alert">
        <span class="stat-icon">&#9201;</span>
        <div>
            <span class="stat-label">Menunggu verifikasi</span>
            <strong class="stat-value stat-value-sm">Rp {{ number_format($totalMenunggu, 0, ',', '.') }}</strong>
            <small>Belum masuk pendapatan</small>
        </div>
    </article>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Rekap Status Pesanan</h2>
            <p class="muted">Jumlah pesanan per status pada periode ini.</p>
        </div>
    </div>
    <div class="status-summary">
        @foreach($rekapStatus as $label => $jumlah)
            <div><span>{{ $label }}</span><strong>{{ $jumlah }}</strong></div>
        @endforeach
    </div>
</section>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Rincian Transaksi</h2>
            <p class="muted">Diurutkan berdasarkan tanggal pesanan.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Jas</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status Bayar</th>
                    <th>Status Pesanan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ $item->tanggal_pesan->format('d/m/Y') }}</td>
                        <td>{{ $item->kode_pesanan }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->jas->nama }}</td>
                        <td>{{ $item->pembayaran?->metode?->label() ?? '-' }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @if($item->pembayaran)
                                <span class="badge badge-{{ $item->pembayaran->status->badge() }}">{{ $item->pembayaran->status->label() }}</span>
                            @else
                                <span class="badge badge-muted">Belum bayar</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $item->badge_tampil }}">{{ $item->status_tampil }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Tidak ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            @if($data->count())
                <tfoot>
                    <tr>
                        <th colspan="5">Total pendapatan terverifikasi</th>
                        <th colspan="3">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</section>
@endsection
