@extends('layouts.admin')

@section('judul', 'Detail Pengguna')
@section('subjudul', $user->name)

@section('isi')
<div class="toolbar">
    <a class="btn btn-outline btn-sm" href="{{ route('admin.pengguna.index') }}">&larr; Kembali</a>
</div>

<div class="admin-grid-2">
    <section class="panel">
        <div class="panel-head">
            <div class="user-cell">
                <span class="avatar avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p class="muted">Customer sejak {{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <dl class="detail-list">
            <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>Nomor HP</dt><dd>{{ $user->nomor_hp ?? '-' }}</dd></div>
            <div><dt>Total pesanan</dt><dd>{{ $user->pesanan->count() }} pesanan</dd></div>
            <div><dt>Total belanja</dt><dd>Rp {{ number_format($user->pesanan->sum('total_harga'), 0, ',', '.') }}</dd></div>
        </dl>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Ringkasan Pesanan</h2>
                <p class="muted">Status pesanan customer ini.</p>
            </div>
        </div>
        <div class="status-summary">
            @foreach(\App\Enums\StatusPesanan::cases() as $status)
                <div>
                    <span>{{ $status->label() }}</span>
                    <strong>{{ $user->pesanan->where('status', $status)->count() }}</strong>
                </div>
            @endforeach
        </div>
    </section>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Riwayat Pesanan</h2>
            <p class="muted">Semua transaksi yang pernah dilakukan.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Jas</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->pesanan as $item)
                    <tr>
                        <td>{{ $item->kode_pesanan }}</td>
                        <td>{{ $item->jas->nama }}</td>
                        <td>{{ $item->tanggal_mulai->format('d/m/Y') }} – {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $item->badge_tampil }}">{{ $item->status_tampil }}</span></td>
                        <td><a class="btn btn-outline btn-xs" href="{{ route('admin.pesanan.show', $item) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-empty">Customer belum pernah memesan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
