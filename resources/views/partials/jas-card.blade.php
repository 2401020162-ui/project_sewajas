@props(['jas'])

<article class="product-card">
    <a class="product-media" href="{{ route('jas.detail', $jas) }}" aria-label="Lihat detail {{ $jas->nama }}">
        @if($jas->gambar_url)
            <img src="{{ $jas->gambar_url }}" alt="{{ $jas->nama }}" loading="lazy">
        @else
            <span class="product-placeholder" aria-hidden="true">
                <span>PW</span>
                <small>Foto jas</small>
            </span>
        @endif
        <span class="product-category">{{ $jas->kategori->nama ?? 'Jas' }}</span>
        @if($jas->stok < 1)
            <span class="product-soldout">Stok habis</span>
        @endif
    </a>

    <div class="product-body">
        <div class="product-meta">
            <span class="rating">
                <span aria-hidden="true">&#9733;</span>
                {{ number_format($jas->rata_rating, 1, ',', '.') }}
            </span>
            <span class="stock {{ $jas->stok < 1 ? 'stock-empty' : ($jas->stok <= 2 ? 'stock-low' : '') }}">
                Stok {{ $jas->stok }}
            </span>
        </div>
        <h3><a href="{{ route('jas.detail', $jas) }}">{{ $jas->nama }}</a></h3>
        <p class="product-price">
            Rp {{ number_format($jas->harga_sewa, 0, ',', '.') }}
            <span>/ hari</span>
        </p>
        <a class="btn btn-outline btn-sm btn-block" href="{{ route('jas.detail', $jas) }}">Lihat Detail</a>
    </div>
</article>
