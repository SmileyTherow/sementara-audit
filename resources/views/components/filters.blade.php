@props([
    'period' => false,
    'status' => false,
    'location' => false,
    'commodity' => false,
    'kwt' => false,
])

@php
    $internal = request()->is('admin*');
    $user = auth()->user();

    $kwtQuery = \App\Models\Kwt::query()
        ->orderBy('name');

    if (
        $internal
        && $user
        && $user->isOperatorKwt()
    ) {
        $kwtQuery->whereKey($user->kwt_id ?? 0);
    }

    $kwts = $kwtQuery->get();

    $commodityQuery = \App\Models\Commodity::query()
        ->where('is_active', true)
        ->with('cultivationCycles.kwt')
        ->orderBy('name');

    if (
        $internal
        && $user
        && $user->isOperatorKwt()
    ) {
        $commodityQuery->whereHas(
            'cultivationCycles',
            fn ($query) => $query->where(
                'kwt_id',
                $user->kwt_id ?? 0
            )
        );
    }

    $commodities = $commodityQuery->get();

    $statusOptions = is_array($status)
        ? $status
        : [
            'planned',
            'planted',
            'growing',
            'ready_to_harvest',
            'harvested',
            'completed',
            'cancelled',
        ];
@endphp

<form
    method="GET"
    action="{{ url()->current() }}"
    class="filters no-print"
>
    <div class="field">
        <label for="filter-q">Cari</label>

        <input
            id="filter-q"
            name="q"
            type="search"
            value="{{ request('q') }}"
            placeholder="Cari nama atau kode..."
        >
    </div>

    @if($kwt)
        <div class="field">
            <label for="filter-kwt">KWT</label>

            <select
                id="filter-kwt"
                name="kwt_id"
            >
                <option value="">Semua KWT</option>

                @foreach($kwts as $kwtOption)
                    <option
                        value="{{ $kwtOption->id }}"
                        @selected(
                            (string) request('kwt_id')
                            === (string) $kwtOption->id
                        )
                    >
                        {{ $kwtOption->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    @if($commodity)
        <div class="field">
            <label for="filter-commodity">Komoditas</label>

            <select
                id="filter-commodity"
                name="commodity_id"
            >
                <option value="">Semua Komoditas</option>

                @foreach($commodities as $commodityOption)
                    <option
                        value="{{ $commodityOption->id }}"
                        @selected(
                            (string) request('commodity_id')
                            === (string) $commodityOption->id
                        )
                    >
                        {{ $commodityOption->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    @if($status)
        <div class="field">
            <label for="filter-status">Status</label>

            <select
                id="filter-status"
                name="status"
            >
                <option value="">Semua Status</option>

                @foreach($statusOptions as $statusOption)
                    <option
                        value="{{ $statusOption }}"
                        @selected(
                            request('status') === $statusOption
                        )
                    >
                        {{ ucfirst(str_replace('_', ' ', $statusOption)) }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    @if($location)
        <div class="field">
            <label for="filter-district">Kecamatan</label>

            <input
                id="filter-district"
                name="district"
                value="{{ request('district') }}"
            >
        </div>

        <div class="field">
            <label for="filter-village">Kelurahan/Desa</label>

            <input
                id="filter-village"
                name="village"
                value="{{ request('village') }}"
            >
        </div>
    @endif

    @if($period)
        <div class="field">
            <label for="filter-start-date">Dari Tanggal</label>

            <input
                id="filter-start-date"
                type="date"
                name="start_date"
                value="{{ request('start_date') }}"
            >
        </div>

        <div class="field">
            <label for="filter-end-date">Sampai Tanggal</label>

            <input
                id="filter-end-date"
                type="date"
                name="end_date"
                value="{{ request('end_date') }}"
            >
        </div>

        <div class="field">
            <label for="filter-month">Bulan</label>

            <input
                id="filter-month"
                type="number"
                min="1"
                max="12"
                name="month"
                value="{{ request('month') }}"
            >
        </div>

        <div class="field">
            <label for="filter-year">Tahun</label>

            <input
                id="filter-year"
                type="number"
                min="1900"
                max="2100"
                name="year"
                value="{{ request('year') }}"
            >
        </div>
    @endif

    <button
        class="btn"
        type="submit"
    >
        Terapkan
    </button>

    <a
        class="text-button"
        href="{{ url()->current() }}"
    >
        Reset
    </a>
</form>