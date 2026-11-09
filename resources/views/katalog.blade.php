@extends('layouts.app')

@section('title', 'Katalog Jas — PW_SEWAJAS')

@section('isi')
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span>/</span>
            <span>Katalog</span>
        </nav>
        <span class="eyebrow eyebrow-light">Koleksi PW_SEWAJAS</span>
        <h1>Katalog Jas</h1>
        <p>Temukan jas yang tepat untuk pernikahan, wisuda, wawancara, atau acara spesialmu.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <form class="filter-search" method="GET" action="{{ route('katalog') }}" role="search">
                <label class="sr-only" for="cari-jas">Cari jas</label>
                <input id="cari-jas" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau deskripsi jas...">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                @if(request('urut'))
                    <input type="hidden" name="urut" value="{{ request('urut') }}">
                @endif
                <button class="btn btn-primary" type="submit">Cari</button>
            </form>

            <form class="filter-sort" method="GET" action="{{ route('katalog') }}">
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <label for="urut">Urutkan</label>
                <select id="urut" name="urut" onchange="this.form.submit()">
                    <option value="">Terbaru</option>
                    <option value="populer" @selected(request('urut') === 'populer')>Paling populer</option>
                    <option value="termurah" @selected(request('urut') === 'termurah')>Harga termurah</option>
                    <option value="termahal" @selected(request('urut') === 'termahal')>Harga termahal</option>
                </select>
            </form>
        </div>

        <div class="chips-filter" aria-label="Filter kategori">
            <a href="{{ route('katalog', array_filter(['q' => request('q'), 'urut' => request('urut')])) }}" class="{{ request('kategori') ? '' : 'active' }}">Semua</a>
            @foreach($kategori as $item)
                <a href="{{ route('katalog', array_filter(['kategori' => $item->slug, 'q' => request('q'), 'urut' => request('urut')])) }}"
                   class="{{ request('kategori') === $item->slug ? 'active' : '' }}">
                    {{ $item->nama }} <span>{{ $item->jas_count }}</span>
                </a>
            @endforeach
        </div>

        <div class="result-bar">
            <p>
                Menampilkan <strong>{{ $jas->count() }}</strong> dari <strong>{{ $jas->total() }}</strong> jas
                @if(request('q'))
                    untuk pencarian &ldquo;{{ request('q') }}&rdquo;
                @endif
            </p>
            @if(request()->hasAny(['q', 'kategori', 'urut']))
                <a class="text-link" href="{{ route('katalog') }}">Reset filter</a>
            @endif
        </div>

        @if($jas->count())
            <div class="product-grid">
                @foreach($jas as $item)
                    @include('partials.jas-card', ['jas' => $item])
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $jas->links('vendor.pagination.custom') }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon" aria-hidden="true">&#128269;</span>
                <strong>Jas tidak ditemukan</strong>
                <p>Coba kata kunci lain atau hapus filter kategori.</p>
                <a class="btn btn-primary" href="{{ route('katalog') }}">Lihat semua jas</a>
            </div>
        @endif
    </div>
</section>
@endsection
