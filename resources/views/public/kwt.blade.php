<x-layout :title="$kwt->name">
    <section class="section">
        <div class="grid-2">
            <img
                class="detail-image"
                src="{{ asset('images/garden.svg') }}"
                alt="Kebun {{ $kwt->name }}"
            >

            <div class="detail-info">
                <span class="eyebrow">
                    {{ $kwt->district ?? 'Tangerang' }}
                    @if($kwt->village)
                        · {{ $kwt->village }}
                    @endif
                </span>

                <h1>{{ $kwt->name }}</h1>

                <p class="prose">
                    {{ $kwt->address ?? 'Alamat belum diisi.' }}
                </p>

                <div class="detail-values">
                    <div>
                        <small>Ketua KWT</small>
                        <strong>{{ $kwt->leader_name ?? '-' }}</strong>
                    </div>

                    <div>
                        <small>Telepon</small>
                        <strong>{{ $kwt->phone ?? '-' }}</strong>
                    </div>

                    <div>
                        <small>Desa/Kelurahan</small>
                        <strong>{{ $kwt->village ?? '-' }}</strong>
                    </div>

                    <div>
                        <small>Kecamatan</small>
                        <strong>{{ $kwt->district ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section soft-section">
        <div class="section-heading">
            <h2>Yang tumbuh di kebun kami</h2>
        </div>

        @php
            $commodities = $kwt->cultivationCycles
                ->pluck('commodity')
                ->filter()
                ->unique('id')
                ->values();
        @endphp

        <div class="grid-4">
            @forelse($commodities as $commodity)
                <x-commodity-card :commodity="$commodity" />
            @empty
                <p class="empty">
                    Belum ada komoditas aktif.
                </p>
            @endforelse
        </div>
    </section>

    <section class="section">
        <h2 style="margin-bottom:25px">Produk olahan</h2>

        <div class="grid-3">
            @forelse($kwt->processedProducts as $product)
                <a
                    class="simple-card"
                    href="{{ url('/produk-olahan/' . $product->id) }}"
                >
                    <span class="product-symbol">🌿</span>

                    <div>
                        <h3>{{ $product->name }}</h3>

                        <div class="small muted">
                            @if($product->is_available)
                                Tersedia
                            @else
                                Tidak tersedia
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p class="muted">
                    Belum ada produk olahan.
                </p>
            @endforelse
        </div>
    </section>
</x-layout>
