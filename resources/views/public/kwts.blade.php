<x-layout title="Jelajahi KWT">
    <div class="page-heading">
        <span class="eyebrow">KOMUNITAS PEREMPUAN BERDAYA</span>
        <h1>Kenali kebun, kenali penggeraknya.</h1>
        <p>
            Kelompok Wanita Tani yang menumbuhkan kebaikan
            di berbagai sudut Kota Tangerang.
        </p>
    </div>

    <section class="section">
        <x-filters :location="true" />

        <div class="grid-3">
            @forelse($records as $kwt)
                @php
                    $commodities = $kwt->cultivationCycles
                        ->pluck('commodity')
                        ->filter()
                        ->unique('id')
                        ->filter(fn ($commodity) => $commodity->is_active);
                @endphp

                <a href="{{ url('/kwt/' . $kwt->id) }}" class="kwt-card">
                    <img
                        src="{{ asset('images/garden.svg') }}"
                        alt="Kebun {{ $kwt->name }}"
                        loading="lazy"
                    >

                    <div>
                        <span class="eyebrow">
                            {{ $kwt->district ?? 'Tangerang' }}
                            @if($kwt->village)
                                · {{ $kwt->village }}
                            @endif
                        </span>

                        <h3>{{ $kwt->name }} →</h3>

                        <p>
                            {{ $commodities->count() }}
                            komoditas aktif
                        </p>
                    </div>
                </a>
            @empty
                <p class="empty">
                    Belum ada KWT yang sesuai filter.
                </p>
            @endforelse
        </div>

        <div class="pagination">
            {{ $records->links() }}
        </div>
    </section>
</x-layout>