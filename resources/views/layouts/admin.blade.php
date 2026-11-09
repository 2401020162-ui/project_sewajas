@php
    $menungguVerifikasi = \App\Models\Pembayaran::where('status', \App\Enums\StatusPembayaran::Pending)->count();
    $menu = [
        ['route' => 'admin.dashboard', 'aktif' => 'admin.dashboard', 'label' => 'Dashboard', 'ikon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
        ['route' => 'admin.jas.index', 'aktif' => 'admin.jas.*', 'label' => 'Kelola Jas', 'ikon' => 'M8 4l4 3 4-3 4 5-3 2v9H7v-9L4 9z'],
        ['route' => 'admin.kategori.index', 'aktif' => 'admin.kategori.*', 'label' => 'Kategori', 'ikon' => 'M4 5h7v7H4zM13 5h7v7h-7zM4 14h7v7H4zM13 14h7v7h-7z'],
        ['route' => 'admin.verifikasi.index', 'aktif' => 'admin.verifikasi.*', 'label' => 'Verifikasi Pembayaran', 'ikon' => 'M4 5h16v14H4zM8 12l3 3 5-6', 'badge' => $menungguVerifikasi],
        ['route' => 'admin.pesanan.index', 'aktif' => 'admin.pesanan.*', 'label' => 'Kelola Pesanan', 'ikon' => 'M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6'],
        ['route' => 'admin.pengguna.index', 'aktif' => 'admin.pengguna.*', 'label' => 'Data Pengguna', 'ikon' => 'M12 12a4 4 0 100-8 4 4 0 000 8zM4 21a8 8 0 0116 0'],
        ['route' => 'admin.laporan', 'aktif' => 'admin.laporan', 'label' => 'Laporan Transaksi', 'ikon' => 'M5 3h14v18H5zM9 8h6M9 12h6M9 16h4'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'Admin') — PW_SEWAJAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body class="admin-body">
<a class="skip-link" href="#konten-admin">Lewati ke konten</a>

<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <a class="brand brand-light" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark">PW</span>
                <span class="brand-text"><strong>PW_SEWAJAS</strong><small>Admin Panel</small></span>
            </a>
            <button class="sidebar-close" type="button" aria-label="Tutup menu" id="sidebarClose">&times;</button>
        </div>

        <nav class="admin-nav" aria-label="Navigasi admin">
            @foreach($menu as $item)
                <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['aktif']) ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="19" height="19" aria-hidden="true"><path d="{{ $item['ikon'] }}" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="nav-badge">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('beranda') }}" class="btn btn-outline btn-sm btn-block">Lihat Situs</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-ghost btn-sm btn-block" type="submit">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="admin-overlay" id="adminOverlay"></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="sidebar-toggle" type="button" aria-label="Buka menu admin" id="sidebarToggle">
                <span></span><span></span><span></span>
            </button>
            <div class="admin-heading">
                <h1>@yield('judul', 'Dashboard')</h1>
                <p>@yield('subjudul', 'Kelola operasional PW_SEWAJAS dari satu tempat.')</p>
            </div>
            <div class="admin-user">
                <span class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                <span class="admin-user-text">
                    <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                    <small>Administrator</small>
                </span>
            </div>
        </header>

        <main class="admin-content" id="konten-admin">
            @include('layouts.pesan')
            @yield('isi')
        </main>
    </div>
</div>

<script>
    (() => {
        const body = document.body;
        const openBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const overlay = document.getElementById('adminOverlay');

        const setOpen = (open) => body.classList.toggle('sidebar-open', open);

        openBtn?.addEventListener('click', () => setOpen(true));
        closeBtn?.addEventListener('click', () => setOpen(false));
        overlay?.addEventListener('click', () => setOpen(false));
    })();
</script>
@stack('scripts')
</body>
</html>
