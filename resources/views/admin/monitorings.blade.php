<x-layout :admin="true" title="Monitoring Tanaman">
    <div class="admin-title">
        <div>
            <span class="eyebrow">PERKEMBANGAN KEBUN</span>
            <h1>Monitoring Tanaman</h1>
            <p>Dokumentasi perkembangan dan proyeksi terbaru dari kebun.</p>
        </div>
        <a href="{{ url('/admin/manage/cycles') }}" class="btn">Pilih Siklus →</a>
    </div>

    <x-filters />

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>KWT / Komoditas</th>
                    <th>Umur</th>
                    <th>Tanaman sehat</th>
                    <th>Proyeksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $m)
                    @php
                        // Prefer the actual relation name 'cultivationCycle'
                        $cycle = $m->relationLoaded('cultivationCycle')
                            ? $m->cultivationCycle
                            : $m->cultivationCycle ?? null;
                        // Fallback values to avoid null dereference in views
                        $kwtName = $cycle && $cycle->kwt ? $cycle->kwt->name : '-';
                        $commodityName = $cycle && $cycle->commodity ? $cycle->commodity->name : '-';
                        $yieldUnit = $cycle->yield_unit ?? '-';
                        // Safe date formatting
                        $monitoringDate = isset($m->monitoring_date) ? $m->monitoring_date->format('d M Y') : '-';
                        $plantAge = $m->plant_age_days ?? '-';
                        $healthyPlants = $m->healthy_plants ?? '-';
                        $projectedYield = $m->projected_yield ?? '-';
                    @endphp
                    <tr>
                        <td>{{ $monitoringDate }}</td>
                        <td>
                            {{ $kwtName }}<br>
                            {{ $commodityName }}
                        </td>
                        <td>{{ $plantAge }} hari</td>
                        <td>{{ $healthyPlants }}</td>
                        <td>{{ $projectedYield }} {{ $yieldUnit }}</td>
                        <td>
                            <a href="{{ url('/admin/cycles/' . $m->cultivation_cycle_id . '#timeline') }}">Lihat
                                dokumentasi ↗</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">Belum ada monitoring.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
</x-layout>
