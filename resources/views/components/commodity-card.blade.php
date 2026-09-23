@props(['commodity'])

@php
    $state = $commodity->public_state;
    $cycle = $state['cycle'] ?? null;
    $kwt = $cycle?->kwt
        ?? $commodity->cultivationCycles->first()?->kwt;
@endphp

<article class="product-card">
    <a href="{{ url('/komoditas/' . $commodity->id) }}" class="card-image">
        <img
            src="{{ asset('images/garden.svg') }}"
            alt="{{ $commodity->name }}"
            loading="lazy"
        >

        <x-badge :status="$state['status']" />
    </a>

    <div class="card-body">
        <small class="eyebrow">
            {{ $kwt?->name ?? 'KWT Tangerang' }}
        </small>

        <h3>
            <a href="{{ url('/komoditas/' . $commodity->id) }}">
                {{ $commodity->name }}
            </a>
        </h3>

        <p class="muted">
            📍 {{ $kwt?->district ?? 'Tangerang' }},
            {{ $kwt?->city ?? 'Tangerang' }}
        </p>

        <div class="card-meta">
            @if($state['status'] === 'Ready')
                <span>Tersedia saat ini</span>
                <strong>
                    {{ number_format((float) $state['quantity'], 2, ',', '.') }}
                    {{ $commodity->default_unit ?? '' }}
                </strong>
            @elseif($cycle?->estimated_harvest_date)
                <span>Estimasi panen</span>
                <strong>
                    {{ $cycle->estimated_harvest_date->translatedFormat('d M Y') }}
                </strong>
            @else
                <span>Status kebun</span>
                <strong>{{ $state['status'] }}</strong>
            @endif
        </div>

        <a
            class="card-link"
            href="{{ url('/komoditas/' . $commodity->id) }}"
        >
            Lihat perkembangan <span>→</span>
        </a>
    </div>
</article>