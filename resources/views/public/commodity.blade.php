<x-layout :title="$commodity->name">
    <section class="commodity-page">
        <a class="text-link" href="{{ url('/komoditas') }}">
            ← Semua komoditas
        </a>

        @php
            $state = $commodity->public_state;
            $cycle = $state['cycle'] ?? $commodity->cultivationCycles->first();
        @endphp

        <div class="commodity-detail-grid">
            <div class="commodity-visual">
                <img
                    class="detail-image"
                    src="{{ asset('images/garden.svg') }}"
                    alt="{{ $commodity->name }}"
                >
            </div>

            <div class="detail-info commodity-info">
                <x-badge :status="$state['status']" />

                <h1>{{ $commodity->name }}</h1>

                @php
                    $kwt = $cycle?->kwt
                        ?? $commodity->cultivationCycles->first()?->kwt;
                @endphp

                <a class="text-link" href="{{ url('/kwt/' . ($kwt?->id ?? 0)) }}">
                    {{ $kwt?->name ?? 'KWT Tangerang' }} →
                </a>

                <p class="muted">
                    📍 {{ $kwt?->district ?? 'Tangerang' }},
                    {{ $kwt?->village ?? '-' }}
                </p>

                <p class="prose">
                    {{ $commodity->description ?? 'Belum ada deskripsi komoditas.' }}
                </p>

                <div class="detail-values">
                    <div>
                        <small>Ketersediaan saat ini</small>
                        <strong>
                            {{ number_format((float) $state['quantity'], 2, ',', '.') }}
                            {{ $commodity->default_unit ?? '' }}
                        </strong>
                    </div>

                    @if($cycle?->estimated_harvest_date)
                        <div>
                            <small>Estimasi panen</small>
                            <strong>
                                {{ $cycle->estimated_harvest_date->translatedFormat('d F Y') }}
                            </strong>
                        </div>
                    @endif

                    @if($cycle?->planting_date)
                        <div>
                            <small>Mulai tanam</small>
                            <strong>
                                {{ $cycle->planting_date->translatedFormat('d M Y') }}
                            </strong>
                        </div>
                    @endif

                    @if($cycle?->target_quantity !== null)
                        <div>
                            <small>Target produksi</small>
                            <strong>
                                {{ number_format((float) $cycle->target_quantity, 2, ',', '.') }}
                                {{ $cycle->target_unit ?? $commodity->default_unit }}
                            </strong>
                        </div>
                    @endif

                    @if($cycle?->target_quantity !== null && $state['quantity'] > 0)
                        <div>
                            <small>Jumlah tersedia</small>
                            <strong>
                                {{ number_format((float) $state['quantity'], 2, ',', '.') }}
                                {{ $cycle->target_unit ?? $commodity->default_unit }}
                            </strong>
                        </div>
                    @endif
                </div>

                @if(in_array($state['status'], ['Ready', 'Pre-Order', 'Sedang Ditanam', 'Segera Panen'], true))
                    <div class="commodity-order">
                        <h2>Hubungi koperasi pusat</h2>
                        <p>
                            Isi kebutuhan Anda, lalu lanjutkan percakapan melalui WhatsApp.
                        </p>

                        <form
                            action="{{ url('/whatsapp/commodity/' . $commodity->id) }}"
                            method="get"
                            class="commodity-order-form"
                        >
                            <div class="field">
                                <label for="quantity">
                                    Jumlah ({{ $commodity->default_unit ?? 'unit' }})
                                </label>

                                <input
                                    type="number"
                                    id="quantity"
                                    name="quantity"
                                    min="0.001"
                                    step="any"
                                    value="1"
                                    required
                                >
                            </div>

                            <button class="btn">
                                {{ match ($state['status']) {
                                    'Ready' => 'Pesan',
                                    'Pre-Order' => 'Pre-Order',
                                    default => 'Tanyakan Pemesanan'
                                }} via WhatsApp →
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if($cycle && $cycle->growthMonitorings->isNotEmpty())
        <section class="commodity-timeline soft-section">
            <div class="commodity-page">
                <span class="eyebrow">DARI BIBIT HINGGA PANEN</span>
                <h2 style="margin-bottom:30px">Cerita tumbuh di kebun.</h2>

                @foreach($cycle->growthMonitorings as $monitor)
                    @php
                        $photos = $monitor->photos;
                    @endphp

                    @if($photos->isNotEmpty())
                        <div class="timeline-item">
                            <h3>
                                {{ $monitor->monitoring_date?->translatedFormat('d M Y') ?? '-' }}
                            </h3>

                            <div class="photo-grid">
                                @foreach($photos as $photo)
                                    <figure>
                                        <img
                                            src="{{ asset('images/garden.svg') }}"
                                            alt="{{ $photo->caption ?? 'Perkembangan tanaman' }}"
                                            loading="lazy"
                                        >

                                        @if($photo->caption)
                                            <figcaption class="small muted">
                                                {{ $photo->caption }}
                                            </figcaption>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
