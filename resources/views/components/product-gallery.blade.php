@props(['record', 'packaged' => false])
@php($photos = \App\Support\ProductImage::gallery($record))
<div class="product-gallery" data-product-gallery>
    <div class="gallery-main">
        <img data-gallery-main src="{{ $photos[0] }}" alt="{{ $record->name }} — Foto Utama" class="gallery-image {{ $packaged ? 'gallery-image-packaged' : '' }}">
    </div>
    @if(count($photos) > 1)
        <div class="gallery-thumbnails" aria-label="Pilih foto {{ $record->name }}">
            @foreach($photos as $index => $photo)
                <button type="button" class="gallery-thumbnail {{ $index === 0 ? 'is-active' : '' }}" data-gallery-src="{{ $photo }}" data-gallery-alt="{{ $record->name }} — {{ $index === 0 ? 'Foto Utama' : 'Foto ' . ($index + 1) }}" aria-label="{{ $index === 0 ? 'Foto Utama' : 'Foto ' . ($index + 1) }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                    <img src="{{ $photo }}" alt="" loading="lazy">
                    <span>{{ $index === 0 ? 'Utama' : $index + 1 }}</span>
                </button>
            @endforeach
        </div>
    @endif
</div>
