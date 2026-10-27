<x-layout :admin="true" title="Panen & Ketersediaan">
    <div class="admin-title">
        <div>
            <span class="eyebrow">HASIL KERJA BAIK</span>
            <h1>Panen & Ketersediaan</h1>
            <p>Catat panen melalui detail siklus budidaya. Ketersediaan dapat diperbarui setelah penyaluran offline.</p>
        </div>
        <a href="{{ url('/admin/manage/cycles') }}" class="btn">Pilih Siklus →</a>
    </div>

    <x-filters :period="true" :commodity="true" />

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode / Tanggal</th>
                    <th>KWT</th>
                    <th>Komoditas</th>
                    <th>Panen ke</th>
                    <th>Kotor</th>
                    <th>Layak</th>
                    <th>Rusak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $h)
                    @php
                        // Harvest has cultivationCycle relation; use it for kwt and commodity
                        $cycle = $h->relationLoaded('cultivationCycle')
                            ? $h->cultivationCycle
                            : $h->cultivationCycle ?? null;
                        $kwtName = $cycle && $cycle->kwt ? $cycle->kwt->name : '-';
                        $commodityName = $cycle && $cycle->commodity ? $cycle->commodity->name : '-';
                        $yieldUnit = $h->yield_unit ?? ($cycle->yield_unit ?? '-');
                        // Safe formatting for date
                        $harvestDate = isset($h->harvest_date) ? $h->harvest_date->format('d M Y') : '-';
                        $harvestCode = $h->harvest_code ?? '-';
                        $grossQty = $h->gross_quantity ?? '-';
                        $usableQty = $h->usable_quantity ?? '-';
                        $damagedQty = $h->damaged_quantity ?? '-';
                    @endphp
                    <tr>
                        <td>{{ $harvestCode }}<br>{{ $harvestDate }}</td>
                        <td>{{ $kwtName }}</td>
                        <td>{{ $commodityName }}</td>
                        <td>{{ $h->harvest_sequence ?? '-' }}</td>
                        <td>{{ $grossQty }} {{ $yieldUnit }}</td>
                        <td>{{ $usableQty }} {{ $yieldUnit }}</td>
                        <td>{{ $damagedQty }} {{ $yieldUnit }}</td>
                        <td>
                            <a href="{{ url('/admin/cycles/' . $h->cultivation_cycle_id . '#availability') }}">Detail
                                & Ketersediaan ↗</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">Belum ada panen untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
</x-layout>
