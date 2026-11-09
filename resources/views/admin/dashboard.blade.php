@extends('layouts.admin')

@section('judul', 'Dashboard')
@section('subjudul', 'Ringkasan operasional PW_SEWAJAS hari ini.')

@section('isi')
@php
    $nilaiMaks = max(1, (int) $pendapatanBulanan->max());
@endphp

<div class="stat-grid">
    <article class="stat-card">
        <span class="stat-icon">&#128203;</span>
        <div>
            <span class="stat-label">Total pesanan</span>
            <strong class="stat-value">{{ number_format($totalPesanan, 0, ',', '.') }}</strong>
            <small>{{ $pesananBulanIni }} pesanan bulan ini</small>
        </div>
    </article>
    <article class="stat-card stat-card-gold">
        <span class="stat-icon">&#128176;</span>
        <div>
            <span class="stat-label">Total pendapatan</span>
            <strong class="stat-value stat-value-sm">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
            <small>Dari pembayaran terverifikasi</small>
        </div>
    </article>
    <article class="stat-card">
        <span class="stat-icon">&#128101;</span>
        <div>
            <span class="stat-label">Pengguna terdaftar</span>
            <strong class="stat-value">{{ number_format($totalPengguna, 0, ',', '.') }}</strong>
            <small>Akun customer aktif</small>
        </div>
    </article>
    <a class="stat-card stat-card-alert" href="{{ route('admin.verifikasi.index') }}">
        <span class="stat-icon">&#9201;</span>
        <div>
            <span class="stat-label">Menunggu verifikasi</span>
            <strong class="stat-value">{{ number_format($menungguVerifikasi, 0, ',', '.') }}</strong>
            <small>Klik untuk memproses &rarr;</small>
        </div>
    </a>
</div>

<div class="admin-grid-2">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Pendapatan 6 Bulan Terakhir</h2>
                <p class="muted">Berdasarkan pesanan dengan pembayaran terverifikasi.</p>
            </div>
        </div>
        <div class="bar-chart" role="img" aria-label="Grafik pendapatan bulanan">
            @foreach($labelBulanan as $key => $label)
                @php
                    $nilai = (int) ($pendapatanBulanan[$key] ?? 0);
                    $tinggi = $nilai > 0 ? max(8, round(($nilai / $nilaiMaks) * 100)) : 2;
                @endphp
                <div class="bar-item">
                    <span class="bar-value">{{ $nilai > 0 ? 'Rp ' . number_format($nilai / 1000, 0, ',', '.') . 'k' : '-' }}</span>
                    <div class="bar-track">
                        <div class="bar-fill" style="height: {{ $tinggi }}%"></div>
                    </div>
                    <span class="bar-label">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Stok Perlu Perhatian</h2>
                <p class="muted">Jas dengan stok 2 unit atau kurang.</p>
            </div>
            <a class="text-link" href="{{ route('admin.jas.index') }}">Kelola &rarr;</a>
        </div>

        @if($stokRendah->count())
            <ul class="low-stock-list">
                @foreach($stokRendah as $item)
                    <li>
                        <div>
                            <strong>{{ $item->nama }}</strong>
                            <small>{{ $item->kategori->nama }}</small>
                        </div>
                        <span class="badge {{ $item->stok < 1 ? 'badge-danger' : 'badge-warning' }}">{{ $item->stok }} unit</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-inline">Semua stok jas dalam kondisi aman.</div>
        @endif

        <div class="quick-actions">
            <a class="btn btn-primary btn-sm" href="{{ route('admin.jas.create') }}">Tambah Jas</a>
            <a class="btn btn-outline btn-sm" href="{{ route('admin.laporan') }}">Cetak Laporan</a>
        </div>
    </section>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Pesanan Terbaru</h2>
            <p class="muted">8 pesanan terakhir yang masuk.</p>
        </div>
        <a class="text-link" href="{{ route('admin.pesanan.index') }}">Lihat semua &rarr;</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Jas</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesananTerbaru as $item)
                    <tr>
                        <td><a class="table-link" href="{{ route('admin.pesanan.show', $item) }}">{{ $item->kode_pesanan }}</a></td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->jas->nama }}</td>
                        <td>{{ $item->tanggal_mulai->format('d/m') }} – {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $item->badge_tampil }}">{{ $item->status_tampil }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-empty">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
