@extends('layouts.app')

@section('title', $jas->nama . ' — PW_SEWAJAS')

@section('isi')
<section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('katalog') }}">Katalog</a>
            <span>/</span>
            <span>{{ $jas->nama }}</span>
        </nav>
    </div>
</section>

<section class="section product-detail-section">
    <div class="container">
        <div class="product-detail">
            <div class="detail-media">
                @if($jas->gambar_url)
                    <img src="{{ $jas->gambar_url }}" alt="{{ $jas->nama }}">
                @else
                    <div class="detail-placeholder" aria-hidden="true">
                        <span>PW</span>
                        <small>Foto jas belum tersedia</small>
                    </div>
                @endif
                <span class="detail-category">{{ $jas->kategori->nama }}</span>
            </div>

            <div class="detail-info">
                <div class="detail-topline">
                    <span class="badge badge-info">{{ $jas->kategori->nama }}</span>
                    <span class="rating rating-lg">
                        <span aria-hidden="true">&#9733;</span>
                        {{ number_format($jas->rata_rating, 1, ',', '.') }}
                        <small>({{ $jas->testimoni->count() }} ulasan)</small>
                    </span>
                </div>

                <h1>{{ $jas->nama }}</h1>
                <p class="detail-price">Rp {{ number_format($jas->harga_sewa, 0, ',', '.') }} <span>/ hari</span></p>
                <p class="detail-desc">{{ $jas->deskripsi ?: 'Jas berkualitas dengan bahan nyaman dan potongan rapi untuk acara spesialmu.' }}</p>

                <div class="detail-facts">
                    <div><span>Stok tersedia</span><strong>{{ $jas->stok }} unit</strong></div>
                    <div><span>Ukuran</span><strong>{{ $jas->ukuran }}</strong></div>
                    <div><span>Durasi</span><strong>Harian</strong></div>
                </div>

                @auth
                    @if(auth()->user()->isCustomer())
                        @if($jas->stok > 0)
                            <form class="booking-form" method="POST" action="{{ route('keranjang.store') }}">
                                @csrf
                                <input type="hidden" name="jas_id" value="{{ $jas->id }}">

                                <div class="field-row">
                                    <div class="field">
                                        <label for="tanggal_mulai">Tanggal mulai sewa</label>
                                        <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                                    </div>
                                    <div class="field">
                                        <label for="tanggal_selesai">Tanggal selesai</label>
                                        <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', now()->addDay()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                                    </div>
                                </div>

                                <fieldset class="size-fieldset">
                                    <legend>Pilih ukuran</legend>
                                    <div class="size-options">
                                        @foreach($jas->ukuran_list as $ukuran)
                                            <label class="size-option">
                                                <input type="radio" name="ukuran" value="{{ $ukuran }}" @checked(old('ukuran') === $ukuran || (! old('ukuran') && $loop->first))>
                                                <span>{{ $ukuran }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                <div class="field quantity-field">
                                    <label for="jumlah">Jumlah</label>
                                    <input id="jumlah" type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" max="{{ min(10, $jas->stok) }}" required>
                                </div>

                                <div class="estimate-box">
                                    <span>Estimasi total</span>
                                    <strong id="estimasi-total">-</strong>
                                </div>

                                <button class="btn btn-primary btn-block btn-lg" type="submit">Tambahkan ke Keranjang</button>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <span class="alert-icon">!</span>
                                <div>Stok jas ini sedang habis. Silakan pilih jas lain atau hubungi admin.</div>
                            </div>
                        @endif
                    @else
                        <a class="btn btn-primary btn-block" href="{{ route('admin.dashboard') }}">Buka Dashboard Admin</a>
                    @endif
                @else
                    <div class="login-cta">
                        <p>Masuk terlebih dahulu untuk menyewa jas ini.</p>
                        <div class="login-cta-actions">
                            <a class="btn btn-primary" href="{{ route('login') }}">Masuk</a>
                            <a class="btn btn-outline" href="{{ route('login', ['tab' => 'daftar']) }}">Daftar</a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</section>

<section class="section section-cream" id="ulasan">
    <div class="container detail-lower-grid">
        <div>
            <div class="section-head">
                <div>
                    <span class="eyebrow">Ulasan pelanggan</span>
                    <h2>Testimoni {{ $jas->nama }}</h2>
                </div>
            </div>

            <div class="review-list">
                @forelse($jas->testimoni->sortByDesc('created_at') as $ulasan)
                    <article class="review-card">
                        <div class="review-head">
                            <span class="avatar">{{ strtoupper(substr($ulasan->user->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $ulasan->user->name }}</strong>
                                <small>{{ $ulasan->created_at->format('d M Y') }}</small>
                            </div>
                            <div class="stars" aria-label="Rating {{ $ulasan->rating }} dari 5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $ulasan->rating ? 'filled' : '' }}">&#9733;</span>
                                @endfor
                            </div>
                        </div>
                        <p>{{ $ulasan->komentar }}</p>
                    </article>
                @empty
                    <div class="empty-state">
                        <strong>Belum ada ulasan untuk jas ini.</strong>
                        <p>Ulasan bisa ditulis setelah pesanan selesai.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="review-form-card">
            @auth
                @if(auth()->user()->isCustomer())
                    @if($sudahSewa && ! $sudahUlas)
                        <h3>Tulis testimoni</h3>
                        <p class="muted">Bagikan pengalamanmu memakai jas ini.</p>
                        <form method="POST" action="{{ route('testimoni.store') }}" class="form">
                            @csrf
                            <input type="hidden" name="jas_id" value="{{ $jas->id }}">
                            <fieldset class="rating-fieldset">
                                <legend>Rating</legend>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" @checked(old('rating', 5) == $i)>
                                        <label for="rating-{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
                                    @endfor
                                </div>
                            </fieldset>
                            <div class="field">
                                <label for="komentar">Komentar</label>
                                <textarea id="komentar" name="komentar" rows="4" maxlength="500" placeholder="Ceritakan pengalamanmu..." required>{{ old('komentar') }}</textarea>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">Kirim Testimoni</button>
                        </form>
                    @elseif($sudahUlas)
                        <div class="notice">
                            <strong>Terima kasih!</strong>
                            <p>Kamu sudah mengirim testimoni untuk jas ini.</p>
                        </div>
                    @else
                        <div class="notice">
                            <strong>Ingin menulis ulasan?</strong>
                            <p>Testimoni dapat ditulis setelah pesanan jas ini berstatus selesai.</p>
                            <a class="text-link" href="{{ route('pesanan.index') }}">Lihat pesanan saya &rarr;</a>
                        </div>
                    @endif
                @endif
            @else
                <div class="notice">
                    <strong>Sudah pernah sewa jas ini?</strong>
                    <p>Masuk ke akunmu untuk menulis testimoni setelah pesanan selesai.</p>
                    <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Masuk</a>
                </div>
            @endauth
        </aside>
    </div>
</section>

@if($terkait->count())
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Rekomendasi</span>
                <h2>Jas lain di kategori {{ $jas->kategori->nama }}</h2>
            </div>
            <a class="text-link" href="{{ route('katalog', ['kategori' => $jas->kategori->slug]) }}">Lihat kategori &rarr;</a>
        </div>
        <div class="product-grid">
            @foreach($terkait as $item)
                @include('partials.jas-card', ['jas' => $item])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    (() => {
        const harga = {{ (int) $jas->harga_sewa }};
        const mulai = document.getElementById('tanggal_mulai');
        const selesai = document.getElementById('tanggal_selesai');
        const jumlah = document.getElementById('jumlah');
        const total = document.getElementById('estimasi-total');

        if (!mulai || !selesai || !jumlah || !total) return;

        const formatRupiah = (nilai) => 'Rp ' + new Intl.NumberFormat('id-ID').format(nilai);

        const hitung = () => {
            if (!mulai.value || !selesai.value || !jumlah.value) {
                total.textContent = '-';
                return;
            }

            const lama = Math.max(1, Math.round((new Date(selesai.value) - new Date(mulai.value)) / 86400000));
            total.textContent = formatRupiah(harga * Number(jumlah.value) * lama) + ' (' + lama + ' hari)';
        };

        mulai.addEventListener('change', () => {
            selesai.min = mulai.value;
            if (selesai.value && selesai.value < mulai.value) selesai.value = mulai.value;
            hitung();
        });

        [selesai, jumlah].forEach((el) => el.addEventListener('input', hitung));
        hitung();
    })();
</script>
@endpush
