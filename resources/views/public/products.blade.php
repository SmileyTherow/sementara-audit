<x-layout title="Produk Olahan">
    <div class="page-heading">
        <span class="eyebrow">KREASI PEREMPUAN TANI</span>
        <h1>Diolah dengan cinta.</h1>
        <p>
            Kenali kreasi olahan KWT dan hubungi koperasi
            untuk informasi ketersediaan.
        </p>
    </div>

    <section class="section">
        <x-filters />

        <div class="grid-3">
            @forelse($records as $product)
                <a
                    class="kwt-card"
                    href="{{ url('/produk-olahan/' . $product->id) }}"
                >
                    <img
                        src="{{ $product->image_path ? Storage::url($product->image_path) : asset('images/garden.svg') }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                    >

                    <div>
                        <small class="eyebrow">
                            {{ $product->kwt?->name ?? 'Koperasi' }}
                        </small>

                        <h3>{{ $product->name }}</h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit($product->description, 110) }}
                        </p>

                        <div class="small muted">
                            @if($product->is_available)
                                Tersedia
                            @else
                                Tidak tersedia
                            @endif
                        </div>

                        @if($product->price !== null)
                            <strong>
                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                / {{ $product->unit }}
                            </strong>
                        @endif
                    </div>
                </a>
            @empty
                <p class="empty">
                    Belum ada produk olahan.
                </p>
            @endforelse
        </div>
    </section>
</x-layout>
