@php
    $cartCount = auth()->check() && auth()->user()->isCustomer()
        ? auth()->user()->keranjang()->sum('jumlah')
        : 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PW_SEWAJAS — Sewa Jas Premium')</title>
    <meta name="description" content="PW_SEWAJAS menyediakan sewa jas pengantin, formal, wisuda, dan almamater dengan koleksi bersih, wangi, dan siap pakai.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body class="@yield('body_class')">
<a class="skip-link" href="#konten">Lewati ke konten</a>

<header class="site-header" id="siteHeader">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('beranda') }}" aria-label="PW_SEWAJAS - Beranda">
            <span class="brand-mark">PW</span>
            <span class="brand-text">
                <strong>PW_SEWAJAS</strong>
                <small>Rental Jas Premium</small>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="mainNav" id="navToggle">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="mainNav" aria-label="Navigasi utama">
            <a class="{{ request()->routeIs('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}">Beranda</a>
            <a class="{{ request()->routeIs('katalog', 'jas.detail') ? 'active' : '' }}" href="{{ route('katalog') }}">Katalog</a>
            <a href="{{ route('beranda') }}#best-seller">Best Seller</a>
            <a href="{{ route('beranda') }}#cara-sewa">Cara Sewa</a>
            <a href="{{ route('beranda') }}#testimoni">Testimoni</a>
            <a href="{{ route('beranda') }}#kontak">Kontak</a>
        </nav>

        <div class="nav-actions">
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-outline btn-sm" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                @else
                    <a class="cart-link" href="{{ route('keranjang.index') }}" aria-label="Keranjang, {{ $cartCount }} item">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="9" cy="20" r="1.4" fill="currentColor"/><circle cx="18" cy="20" r="1.4" fill="currentColor"/><path d="M6 6L5 3H2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>Keranjang</span>
                        <span class="cart-badge">{{ $cartCount }}</span>
                    </a>
                    <a class="nav-user" href="{{ route('pesanan.index') }}">{{ auth()->user()->name }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button class="btn btn-ghost btn-sm" type="submit">Keluar</button>
                </form>
            @else
                <a class="btn btn-ghost btn-sm" href="{{ route('login') }}">Masuk</a>
                <a class="btn btn-primary btn-sm" href="{{ route('login', ['tab' => 'daftar']) }}">Daftar</a>
            @endauth
        </div>
    </div>
</header>

<main id="konten">
    <div class="container flash-wrap">
        @include('layouts.pesan')
    </div>
    @yield('isi')
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand brand-light" href="{{ route('beranda') }}">
                <span class="brand-mark">PW</span>
                <span class="brand-text"><strong>PW_SEWAJAS</strong><small>Rental Jas Premium</small></span>
            </a>
            <p>Sewa jas online tanpa ribet. Koleksi bersih, wangi, dan disetrika rapi sebelum kamu pakai.</p>
            <div class="footer-social">
                <a href="https://www.instagram.com/" target="_blank" rel="noopener">Instagram</a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noopener">TikTok</a>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </div>

        <div>
            <h3>Navigasi</h3>
            <ul class="footer-links">
                <li><a href="{{ route('beranda') }}">Beranda</a></li>
                <li><a href="{{ route('katalog') }}">Katalog Jas</a></li>
                <li><a href="{{ route('beranda') }}#cara-sewa">Cara Sewa</a></li>
                <li><a href="{{ route('beranda') }}#testimoni">Testimoni</a></li>
            </ul>
        </div>

        <div>
            <h3>Bantuan</h3>
            <ul class="footer-links">
                @auth
                    <li><a href="{{ route('pesanan.index') }}">Pesanan Saya</a></li>
                    <li><a href="{{ route('keranjang.index') }}">Keranjang</a></li>
                @else
                    <li><a href="{{ route('login') }}">Masuk / Daftar</a></li>
                @endauth
                <li><a href="{{ route('beranda') }}#kontak">Hubungi Kami</a></li>
                <li><a href="{{ route('beranda') }}#faq">Pertanyaan Umum</a></li>
            </ul>
        </div>

        <div>
            <h3>Kontak</h3>
            <ul class="footer-contact">
                <li><span>Alamat</span>Jl. Merdeka No. 21, Tanjungpinang, Kepulauan Riau</li>
                <li><span>WhatsApp</span><a href="https://wa.me/6281234567890">0812-3456-7890</a></li>
                <li><span>Jam Buka</span>Senin–Sabtu, 09.00–20.00 WIB</li>
                <li><span>Email</span><a href="mailto:halo@sewajas.test">halo@sewajas.test</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <span>&copy; {{ date('Y') }} PW_SEWAJAS. Semua hak dilindungi.</span>
            <span>Dibuat untuk memudahkan kamu tampil percaya diri.</span>
        </div>
    </div>
</footer>

<script>
    (() => {
        const header = document.getElementById('siteHeader');
        const toggle = document.getElementById('navToggle');
        const nav = document.getElementById('mainNav');

        if (toggle) {
            toggle.addEventListener('click', () => {
                const open = header.classList.toggle('nav-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.querySelectorAll('#mainNav a, .nav-actions a').forEach((link) => {
            link.addEventListener('click', () => {
                header.classList.remove('nav-open');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        });

        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 8);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    })();
</script>
@stack('scripts')
</body>
</html>
