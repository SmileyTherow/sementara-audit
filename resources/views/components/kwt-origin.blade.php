@props(['kwt' => null])

<div class="kwt-origin">
    <span class="kwt-origin-avatar" aria-hidden="true">{{ $kwt ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(str_replace('KWT ', '', $kwt->name), 0, 1)) : 'K' }}</span>
    <div class="kwt-origin-info">
        @if($kwt)
            <a class="kwt-origin-name" href="{{ url('/kwt/' . $kwt->id) }}">{{ $kwt->name }}</a>
            <span class="kwt-origin-location">{{ collect([$kwt->village, $kwt->district, $kwt->city])->filter()->join(', ') }}</span>
        @else
            <strong class="kwt-origin-name">Koperasi KWT Tangerang</strong>
            <span class="kwt-origin-location">Informasi KWT asal belum tersedia.</span>
        @endif
    </div>
    @if($kwt)
        <a class="btn btn-outline kwt-origin-button" href="{{ url('/kwt/' . $kwt->id) }}">Lihat KWT <span aria-hidden="true">→</span></a>
    @endif
</div>
