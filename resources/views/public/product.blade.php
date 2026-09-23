<x-layout :title="$product->name">
    <section class="section">
        <div class="grid-2">
            <img
                class="detail-image"
                src="{{ $product->image_path ? Storage::url($product->image_path) : asset('images/garden.svg') }}"
                alt="{{ $product->name }}"
            >

            <div class="detail-info">
                <span class="eyebrow">
                    {{ $product->kwt?->name ?? 'Koperasi' }}
                </span>

                <h1>{{ $product->name }}</h1>

                <div class="small muted">
                    @if($product->is_available)
                        Tersedia
                    @else
                        Tidak tersedia
                    @endif
                </div>

                <p class="prose">
                    {{ $product->description }}
                </p>

                @if($product->price !== null)
                    <h3>
                        Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                        / {{ $product->unit }}
                    </h3>
                @endif

                <p class="small muted">
                    Stok:
                    {{ $product->stock !== null ? number_format((float) $product->stock, 2, ',', '.') : 'Belum ada' }}
                    {{ $product->unit ?? '' }}
                </p>

                @if($product->is_available)
                    <form
                        method="get"
                        action="{{ url('/whatsapp/product/' . $product->id) }}"
                        class="actions"
                    >
                        <div class="field">
                            <label for="quantity">
                                Jumlah ({{ $product->unit ?? 'unit' }})
                            </label>

                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="1"
                                min="0.001"
                                step="any"
                                required
                            >
                        </div>

                        <button class="btn">
                            Pesan via WhatsApp →
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layout>
