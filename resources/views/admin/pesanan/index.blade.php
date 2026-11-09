@extends('layouts.admin')

@section('judul', 'Kelola Pesanan')
@section('subjudul', 'Pantau dan proses pesanan dari pembayaran hingga pengembalian jas.')

@section('isi')
<div class="toolbar toolbar-wrap">
    <form class="toolbar-filters" method="GET" action="{{ route('admin.pesanan.index') }}">
        <div class="field">
            <label class="sr-only" for="q">Cari pesanan</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Cari kode atau nama pelanggan...">
        </div>
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <button class="btn btn-outline btn-sm" type="submit">Cari</button>
        @if(request()->hasAny(['q', 'status']))
            <a class="text-link" href="{{ route('admin.pesanan.index') }}">Reset</a>
        @endif
    </form>
</div>

<div class="chips-filter chips-admin">
    <a href="{{ route('admin.pesanan.index', array_filter(['q' => request('q')])) }}" class="{{ request('status') ? '' : 'active' }}">Semua</a>
    @foreach(\App\Enums\StatusPesanan::cases() as $status)
        <a href="{{ route('admin.pesanan.index', array_filter(['status' => $status->value, 'q' => request('q')])) }}"
           class="{{ request('status') === $status->value ? 'active' : '' }}">
            {{ $status->label() }}
        </a>
    @endforeach
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Jas</th>
                    <th>Jadwal Sewa</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td><a class="table-link" href="{{ route('admin.pesanan.show', $item) }}">{{ $item->kode_pesanan }}</a></td>
                        <td>
                            <strong>{{ $item->user->name }}</strong>
                            <small class="table-sub">{{ $item->user->nomor_hp }}</small>
                        </td>
                        <td>
                            {{ $item->jas->nama }}
                            <small class="table-sub">{{ $item->ukuran }} &middot; {{ $item->jumlah }} unit</small>
                        </td>
                        <td>{{ $item->tanggal_mulai->format('d/m/Y') }} – {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $item->badge_tampil }}">{{ $item->status_tampil }}</span></td>
                        <td>
                            @if($item->pembayaran)
                                <span class="badge badge-{{ $item->pembayaran->status->badge() }}">{{ $item->pembayaran->status->label() }}</span>
                            @else
                                <span class="badge badge-muted">Belum bayar</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-outline btn-xs" href="{{ route('admin.pesanan.show', $item) }}">Detail</a>

                                @if($item->status === \App\Enums\StatusPesanan::Disetujui)
                                    <form method="POST" action="{{ route('admin.pesanan.kirim', $item) }}" onsubmit="return confirm('Tandai pesanan ini sedang disewa/dikirim?')">
                                        @csrf
                                        <button class="btn btn-primary btn-xs" type="submit">Kirim</button>
                                    </form>
                                @elseif($item->status === \App\Enums\StatusPesanan::Dikirim)
                                    <form method="POST" action="{{ route('admin.pesanan.selesai', $item) }}" onsubmit="return confirm('Tandai jas sudah dikembalikan dan selesaikan pesanan?')">
                                        @csrf
                                        <button class="btn btn-primary btn-xs" type="submit">Selesai</button>
                                    </form>
                                @endif

                                @if($item->status->bisaDibatalkan())
                                    <form method="POST" action="{{ route('admin.pesanan.batal', $item) }}" onsubmit="return confirm('Batalkan pesanan {{ $item->kode_pesanan }}?')">
                                        @csrf
                                        <button class="btn btn-ghost btn-xs text-danger" type="submit">Batal</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Tidak ada pesanan yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $pesanan->links('vendor.pagination.custom') }}
    </div>
</section>
@endsection
