<x-layout :admin="true" title="Ringkasan Kebun">
    <div class="admin-title">
        <div>
            <span class="eyebrow">
                {{ auth()->user()->kwt?->name ?? 'DASHBOARD KOPERASI' }}
            </span>

            <h1>
                Selamat datang,
                {{ Str::before(auth()->user()->name, ' ') }}
            </h1>

            <p>
                Mari lihat kabar kebun dan hasil kerja baik hari ini.
            </p>
        </div>

        @if(! auth()->user()->isKoperasi())
            <a href="{{ url('/admin/manage/cycles/create') }}" class="btn">
                + Mulai Budidaya
            </a>
        @endif
    </div>

    <div class="metric-grid">
        <div class="metric">
            <span>KWT Aktif</span>
            <strong>{{ $kwtCount }}</strong>
            <small>Komunitas yang tumbuh bersama</small>
        </div>

        <div class="metric">
            <span>Komoditas Aktif</span>
            <strong>{{ $commodities->count() }}</strong>
            <small>Komoditas dalam sistem</small>
        </div>

        <div class="metric">
            <span>Siklus Berjalan</span>
            <strong>{{ $cycles->count() }}</strong>
            <small>Siklus budidaya aktif</small>
        </div>

        <div class="metric">
            <span>Hasil Tersedia</span>
            <strong>
                {{ $commodities->filter(
                    fn ($commodity) => $commodity->public_state['status'] === 'Ready'
                )->count() }}
            </strong>
            <small>Komoditas siap ditawarkan</small>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <h2>Ringkasan periode ini</h2>

            <p class="small muted">
                {{ $report['start'] }} sampai {{ $report['end'] }}
            </p>

            @forelse($report['totalsByUnit'] as $unit => $total)
                <div class="detail-values">
                    <div>
                        <small>Target</small>
                        <strong>
                            {{ number_format($total['expected'], 2, ',', '.') }}
                            {{ $unit }}
                        </strong>
                    </div>

                    <div>
                        <small>Realisasi</small>
                        <strong>
                            {{ number_format($total['actual'], 2, ',', '.') }}
                            {{ $unit }}
                        </strong>
                    </div>
                </div>
            @empty
                <p class="empty">
                    Belum ada data pada periode ini.
                </p>
            @endforelse

            @php
                $achievements = $report['rows']
                    ->whereNotNull('achievement');
            @endphp

            <p class="small">
                Rata-rata capaian:
                {{ $achievements->isNotEmpty()
                    ? number_format($achievements->avg('achievement'), 2, ',', '.') . ' %'
                    : '—' }}
            </p>

            <a href="{{ url('/admin/reports') }}" class="text-link">
                Buka laporan lengkap →
            </a>
        </div>

        <div class="panel">
            <h2>Jadwal terdekat</h2>

            @forelse($cycles->take(5) as $cycle)
                <a
                    class="schedule-row"
                    href="{{ url('/admin/cycles/' . $cycle->id) }}"
                >
                    <span class="date-block">
                        @if($cycle->estimated_harvest_date)
                            <strong>
                                {{ $cycle->estimated_harvest_date->format('d') }}
                            </strong>

                            {{ $cycle->estimated_harvest_date->translatedFormat('M') }}
                        @else
                            <strong>-</strong>
                        @endif
                    </span>

                    <span>
                        <strong>{{ $cycle->commodity?->name ?? '-' }}</strong>
                        <small>{{ $cycle->kwt?->name ?? '-' }}</small>
                    </span>

                    <x-badge :status="$cycle->status" />
                </a>
            @empty
                <p class="empty">
                    Belum ada siklus berjalan.
                </p>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="section-heading">
            <div>
                <h2>Perjalanan hasil panen</h2>
                <p class="chart-key">
                    Hijau muda: target ·
                    <b>Hijau tua: realisasi</b>
                </p>
            </div>

            <select data-trend aria-label="Periode grafik">
                <option value="12">12 bulan terakhir</option>
                <option value="6">6 bulan terakhir</option>
            </select>
        </div>

        @php
            $units = $trend
                ->flatMap(fn ($item) =>
                    $item['expected']->keys()
                        ->merge($item['actual']->keys())
                )
                ->unique();
        @endphp

        @forelse($units as $unit)
            @php
                $max = max(
                    1,
                    $trend->max(fn ($item) => max(
                        $item['expected'][$unit] ?? 0,
                        $item['actual'][$unit] ?? 0
                    ))
                );
            @endphp

            <strong class="small">Satuan {{ $unit }}</strong>

            <div
                class="chart"
                role="img"
                aria-label="Grafik hasil {{ $unit }}"
            >
                @foreach($trend as $index => $month)
                    <div
                        class="chart-col"
                        data-index="{{ $index }}"
                    >
                        <span style="height:{{ (($month['expected'][$unit] ?? 0) / $max) * 95 }}%"></span>
                        <span style="height:{{ (($month['actual'][$unit] ?? 0) / $max) * 95 }}%"></span>
                        <small>{{ $month['label'] }}</small>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="empty">
                Grafik tampil setelah data budidaya atau panen dicatat.
            </p>
        @endforelse
    </div>

    <div class="section-heading">
        <h2>Kebun yang sedang tumbuh</h2>
        <a href="{{ url('/admin/manage/cycles') }}" class="text-link">
            Semua siklus →
        </a>
    </div>

    <div class="grid-2">
        @forelse($cycles->take(6) as $cycle)
            <div class="panel">
                <div class="section-heading">
                    <div>
                        <small class="eyebrow">
                            {{ $cycle->kwt?->name ?? '-' }}
                        </small>

                        <h3>{{ $cycle->commodity?->name ?? '-' }}</h3>
                    </div>

                    <x-badge :status="$cycle->status" />
                </div>

                <p class="small muted">
                    Mulai tanam:
                    {{ $cycle->planting_date?->translatedFormat('d M Y') ?? '-' }}
                </p>

                <div class="detail-values">
                    <div>
                        <small>Target produksi</small>
                        <strong>
                            {{ number_format((float) $cycle->target_quantity, 2, ',', '.') }}
                            {{ $cycle->target_unit ?? '' }}
                        </strong>
                    </div>

                    <div>
                        <small>Luas tanam</small>
                        <strong>
                            {{ number_format((float) $cycle->planting_area, 2, ',', '.') }}
                        </strong>
                    </div>
                </div>

                <a
                    class="btn btn-light"
                    href="{{ url('/admin/cycles/' . $cycle->id) }}"
                >
                    Monitoring & Panen →
                </a>
            </div>
        @empty
            <p class="empty">
                Belum ada siklus budidaya.
            </p>
        @endforelse
    </div>
</x-layout>
