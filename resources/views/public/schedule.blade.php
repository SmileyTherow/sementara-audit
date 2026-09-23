<x-layout title="Jadwal Panen">
    <div class="page-heading">
        <span class="eyebrow">SEGAR SEBENTAR LAGI</span>
        <h1>Jadwal panen kebun kita.</h1>
        <p>
            Temukan estimasi waktu panen dan target hasil.
            Jadwal dapat berubah mengikuti kondisi tanaman.
        </p>
    </div>

    <section class="section">
        <x-filters
            :period="true"
            :location="true"
            :commodity="true"
        />

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Estimasi panen</th>
                        <th>Komoditas</th>
                        <th>KWT</th>
                        <th>Target</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records as $cycle)
                        <tr>
                            <td>
                                @if($cycle->estimated_harvest_date)
                                    {{ $cycle->estimated_harvest_date->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ url('/komoditas/' . $cycle->commodity_id) }}">
                                    {{ $cycle->commodity?->name ?? '-' }} →
                                </a>
                            </td>

                            <td>
                                {{ $cycle->kwt?->name ?? '-' }}

                                @if($cycle->kwt?->district)
                                    <br>
                                    <small>{{ $cycle->kwt->district }}</small>
                                @endif
                            </td>

                            <td>
                                @if($cycle->target_quantity !== null)
                                    ±
                                    {{ number_format((float) $cycle->target_quantity, 2, ',', '.') }}
                                    {{ $cycle->target_unit }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <x-badge
                                    :status="$cycle->commodity?->public_state['status'] ?? $cycle->status"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                Belum ada jadwal panen sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $records->links() }}
    </section>
</x-layout>