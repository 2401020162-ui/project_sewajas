@extends('layouts.app')

@section('title', 'PW_SEWAJAS — Sewa Jas Premium Tanjungpinang')

@section('isi')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Rental Jas Premium Tanjungpinang</span>
            <h1>Tampil <em>Elegan</em> di Setiap Momen Spesial</h1>
            <p>Sewa jas pengantin, formal, wisuda, dan almamater tanpa ribet. Koleksi bersih, wangi, disetrika rapi, dan siap pakai saat kamu ambil.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ route('katalog') }}">Lihat Katalog</a>
                <a class="btn btn-outline" href="#best-seller">Jas Terpopuler</a>
            </div>
            <div class="hero-stats">
                <div><strong>500+</strong><span>Pelanggan puas</span></div>
                <div><strong>50+</strong><span>Koleksi jas</span></div>
                <div><strong>4.8/5</strong><span>Rating pelanggan</span></div>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="hero-frame">
                <div class="hero-monogram">PW</div>
                <p>Premium Suit Rental</p>
                <span>Since 2021</span>
            </div>
            <div class="hero-card hero-card-top">
                <span class="hero-card-icon">&#10003;</span>
                <div><strong>Bersih &amp; wangi</strong><small>Dicuci setiap habis dipakai</small></div>
            </div>
            <div class="hero-card hero-card-bottom">
                <span class="hero-card-icon">&#9733;</span>
                <div><strong>4.8 dari 5</strong><small>Ulasan pelanggan asli</small></div>
            </div>
        </div>
    </div>
</section>

<section class="feature-strip">
    <div class="container feature-grid">
        <div class="feature-item">
            <span class="feature-icon">&#10003;</span>
            <div><strong>Higienis</strong><p>Dicuci dan disetrika sebelum diserahkan.</p></div>
        </div>
        <div class="feature-item">
            <span class="feature-icon">&#9201;</span>
            <div><strong>Fleksibel</strong><p>Sewa harian, ambil langsung di toko.</p></div>
        </div>
        <div class="feature-item">
            <span class="feature-icon">&#9733;</span>
            <div><strong>Ukuran lengkap</strong><p>Tersedia ukuran S sampai XXL.</p></div>
        </div>
        <div class="feature-item">
            <span class="feature-icon">&#128179;</span>
            <div><strong>Bayar mudah</strong><p>Transfer, QRIS, atau cash di toko.</p></div>
        </div>
    </div>
</section>

<section class="section" id="kategori">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Kategori unggulan</span>
                <h2>Temukan jas sesuai acaramu</h2>
            </div>
            <a class="text-link" href="{{ route('katalog') }}">Lihat semua jas &rarr;</a>
        </div>

        <div class="category-grid">
            @forelse($kategori as $item)
                <a class="category-card" href="{{ route('katalog', ['kategori' => $item->slug]) }}">
                    <span class="category-icon" aria-hidden="true">{{ strtoupper(substr($item->nama, 0, 1)) }}</span>
                    <span class="category-info">
                        <strong>{{ $item->nama }}</strong>
                        <small>{{ $item->jas_count }} koleksi tersedia</small>
                    </span>
                    <span class="category-arrow" aria-hidden="true">&rarr;</span>
                </a>
            @empty
                <p class="muted">Belum ada kategori yang tersedia.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="section section-cream" id="best-seller">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Paling banyak disewa</span>
                <h2>Best Seller</h2>
                <p class="section-sub">Jas favorit pelanggan PW_SEWAJAS yang paling sering dipilih.</p>
            </div>
            <a class="text-link" href="{{ route('katalog', ['urut' => 'populer']) }}">Semua best seller &rarr;</a>
        </div>

        @if($populer->count())
            <div class="product-grid">
                @foreach($populer as $jas)
                    @include('partials.jas-card', ['jas' => $jas])
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <strong>Belum ada jas yang tersedia.</strong>
                <p>Silakan cek kembali dalam waktu dekat.</p>
            </div>
        @endif
    </div>
</section>

<section class="section" id="cara-sewa">
    <div class="container">
        <div class="section-head center">
            <div>
                <span class="eyebrow">Cara sewa</span>
                <h2>Empat langkah, jas siap dipakai</h2>
            </div>
        </div>

        <div class="steps">
            <article class="step">
                <span class="step-number">01</span>
                <h3>Pilih jas</h3>
                <p>Telusuri katalog, pilih ukuran, dan tentukan tanggal sewa.</p>
            </article>
            <article class="step">
                <span class="step-number">02</span>
                <h3>Checkout</h3>
                <p>Masukkan jas ke keranjang lalu kirim pesanan dari akunmu.</p>
            </article>
            <article class="step">
                <span class="step-number">03</span>
                <h3>Bayar</h3>
                <p>Transfer, QRIS, atau bayar cash. Unggah bukti jika diperlukan.</p>
            </article>
            <article class="step">
                <span class="step-number">04</span>
                <h3>Ambil &amp; pakai</h3>
                <p>Setelah diverifikasi, ambil jas di toko dan tampil percaya diri.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section-cream" id="testimoni">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Kata pelanggan</span>
                <h2>Testimoni</h2>
                <p class="section-sub">Pengalaman nyata pelanggan yang sudah menyewa di PW_SEWAJAS.</p>
            </div>
            @auth
                @if(auth()->user()->isCustomer())
                    <a class="text-link" href="{{ route('pesanan.index') }}">Pesanan saya &rarr;</a>
                @endif
            @endauth
        </div>

        <div class="testimonial-grid">
            @forelse($testimoni as $item)
                <article class="testimonial-card">
                    <div class="stars" aria-label="Rating {{ $item->rating }} dari 5">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $item->rating ? 'filled' : '' }}">&#9733;</span>
                        @endfor
                    </div>
                    <p>&ldquo;{{ $item->komentar }}&rdquo;</p>
                    <footer>
                        <span class="avatar">{{ strtoupper(substr($item->user->name, 0, 1)) }}</span>
                        <div>
                            <strong>{{ $item->user->name }}</strong>
                            <small>{{ $item->jas->nama ?? 'PW_SEWAJAS' }} &middot; {{ $item->created_at->format('d/m/Y') }}</small>
                        </div>
                    </footer>
                </article>
            @empty
                <div class="empty-state">
                    <strong>Belum ada testimoni.</strong>
                    <p>Testimoni dapat ditulis setelah pesananmu selesai.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="section" id="faq">
    <div class="container faq-grid">
        <div>
            <span class="eyebrow">Pertanyaan umum</span>
            <h2>Butuh bantuan sebelum menyewa?</h2>
            <p class="muted">Berikut beberapa pertanyaan yang paling sering ditanyakan pelanggan.</p>
            <a class="btn btn-primary" href="https://wa.me/6281234567890" target="_blank" rel="noopener">Chat WhatsApp</a>
        </div>
        <div class="faq-list">
            <details open>
                <summary>Apakah bisa sewa hanya satu hari?</summary>
                <p>Bisa. Minimal sewa adalah satu hari dan dihitung per tanggal mulai sampai tanggal selesai.</p>
            </details>
            <details>
                <summary>Bagaimana cara membayar?</summary>
                <p>Kami menerima transfer bank, QRIS, dan cash di toko. Untuk transfer/QRIS, unggah bukti pembayaran melalui halaman pesanan.</p>
            </details>
            <details>
                <summary>Apakah jas sudah bersih?</summary>
                <p>Ya. Setiap jas dicuci dan disetrika setelah dikembalikan, lalu diperiksa kembali sebelum diserahkan ke pelanggan berikutnya.</p>
            </details>
            <details>
                <summary>Apakah bisa memesan untuk acara besok?</summary>
                <p>Bisa, selama stok tersedia. Kami menyarankan memesan minimal 1–2 hari sebelum acara agar ukuran pilihan masih lengkap.</p>
            </details>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <span class="eyebrow eyebrow-light">Siap tampil memukau?</span>
            <h2>Jas terbaikmu sudah menunggu.</h2>
            <p>Pilih koleksi favoritmu sekarang dan selesaikan pemesanan dalam hitungan menit.</p>
        </div>
        <a class="btn btn-light" href="{{ route('katalog') }}">Mulai Sewa</a>
    </div>
</section>
@endsection
