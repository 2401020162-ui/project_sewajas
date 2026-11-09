@extends('layouts.admin')

@section('judul', 'Data Pengguna')
@section('subjudul', 'Daftar customer dan riwayat transaksi mereka.')

@section('isi')
<div class="toolbar">
    <form class="toolbar-filters" method="GET" action="{{ route('admin.pengguna.index') }}">
        <div class="field">
            <label class="sr-only" for="q">Cari pengguna</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau nomor HP...">
        </div>
        <button class="btn btn-outline btn-sm" type="submit">Cari</button>
        @if(request('q'))
            <a class="text-link" href="{{ route('admin.pengguna.index') }}">Reset</a>
        @endif
    </form>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor HP</th>
                    <th>Jumlah Pesanan</th>
                    <th>Terdaftar</th>
                    <th class="th-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <span class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->nomor_hp ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $user->pesanan_count }} pesanan</span></td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td><a class="btn btn-outline btn-xs" href="{{ route('admin.pengguna.show', $user) }}">Lihat Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-empty">Tidak ada pengguna yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $users->links('vendor.pagination.custom') }}
    </div>
</section>
@endsection
