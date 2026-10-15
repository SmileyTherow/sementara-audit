<x-layout>
    <section class="hero">
        <div class="hero-copy">
            <span class="hero-kicker">
                <span class="dot"></span>
                DARI TANGAN PEREMPUAN, UNTUK KOTA
            </span>

            <h1>
                Dari kebun KWT,<br>
                kebaikan untuk<br>
                <em>Kota Tangerang.</em>
            </h1>

            <p>
                Kenali kebunnya, ikuti pertumbuhannya, nikmati hasilnya.
                Bersama perempuan tani, kita tumbuhkan kota yang lebih hijau
                dan berdaya.
            </p>

            <div class="actions">
                <a href="{{ url('/komoditas') }}" class="btn">
                    Jelajahi Hasil Kebun →
                </a>

                <a href="{{ url('/jadwal-panen') }}" class="btn btn-outline">
                    Lihat Jadwal Panen →
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <img
                src="{{ asset('images/garden.svg') }}"
                alt="Kebun KWT Kota Tangerang"
            >

            <div class="image-label">
                KEBUN KITA, KOTA TANGERANG
            </div>

            <div class="floating-card">
                <div>
                    <small>DARI BIBIT MENJADI MANFAAT</small>
                    <strong>Segar dari kebun lokal</strong>
                    <span>Terpantau tumbuh, terjaga kualitasnya</span>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-strip">
        <div>
            <strong>{{ \App\Models\Kwt::where('is_active', true)->count() }}</strong>
            <span>KWT bergabung</span>
        </div>

        <div>
            <strong>{{ $all->count() }}</strong>
            <span>Komoditas aktif</span>
        </div>

        <div>
            <strong>
                {{ $all->filter(fn ($commodity) => $commodity->public_state['status'] === 'Sedang Ditanam')->count() }}
            </strong>
            <span>Sedang ditanam</span>
        </div>

        <div>
            <strong>
                {{ $all->filter(fn ($commodity) => $commodity->public_state['status'] === 'Segera Panen')->count() }}
            </strong>
            <span>Menjelang panen</span>
        </div>

        <div>
            <strong>
                {{ $all->filter(fn ($commodity) => $commodity->public_state['status'] === 'Ready')->count() }}
            </strong>
            <span>Hasil tersedia</span>
        </div>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">KABAR BAIK DARI KEBUN</span>
                <h2>Apa yang tumbuh hari ini?</h2>
                <p class="muted">
                    Temukan komoditas dan ikuti perjalanannya dari tanam hingga panen.
                </p>
            </div>

            <a href="{{ url('/komoditas') }}" class="text-link">
                Lihat Semua Komoditas →
            </a>
        </div>

        <div class="grid-4">
            @forelse($featured as $commodity)
                <x-commodity-card :commodity="$commodity" />
            @empty
                <p class="empty">
                    Belum ada komoditas aktif.
                </p>
            @endforelse
        </div>
    </section>

    <section class="harvest-section">
        <div>
            <span class="eyebrow">CATAT TANGGALNYA</span>

            <h2>
                Yang segar,<br>
                sebentar lagi.
            </h2>

            <p>
                Pantau jadwal panen terdekat dan hubungi koperasi
                untuk informasi ketersediaan.
            </p>

            <a href="{{ url('/jadwal-panen') }}" class="btn btn-outline">
                Kalender panen →
            </a>
        </div>

        <div class="schedule-list">
            @forelse($schedule as $cycle)
                <a
                    href="{{ url('/komoditas/' . $cycle->commodity_id) }}"
                    class="schedule-row"
                >
                    <span class="date-block">
                        @if($cycle->estimated_harvest_date)
                            <strong>
                                {{ $cycle->estimated_harvest_date->format('d') }}
                            </strong>

                            {{ $cycle->estimated_harvest_date->translatedFormat('M') }}
                        @else
                            <strong>-</strong>
                            -
                        @endif
                    </span>

                    <span>
                        <strong>
                            {{ $cycle->commodity?->name ?? 'Tanaman' }}
                        </strong>

                        <small>
                            {{ $cycle->kwt?->name ?? 'KWT' }}
                        </small>
                    </span>

                    <span class="schedule-yield">
                        @if($cycle->target_quantity)
                            ±
                            {{ number_format((float) $cycle->target_quantity, 1, ',', '.') }}
                            {{ $cycle->target_unit }}
                        @else
                            Target belum tersedia
                        @endif

                        <small>Target produksi</small>
                    </span>

                    <span>→</span>
                </a>
            @empty
                <p>
                    Belum ada jadwal panen.
                </p>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">PEREMPUAN HEBAT, KEBUN BERDAYA</span>
                <h2>Kenali komunitas di baliknya.</h2>
            </div>

            <a href="{{ url('/kwt') }}" class="text-link">
                Jelajahi semua KWT →
            </a>
        </div>

        <div class="grid-3">
            @forelse($kwts as $kwt)
                <a
                    href="{{ url('/kwt/' . $kwt->id) }}"
                    class="kwt-card"
                >
                    <img
                        src="{{ asset('images/garden.svg') }}"
                        alt="Kebun {{ $kwt->name }}"
                        loading="lazy"
                    >

                    <div>
                        <span class="eyebrow">
                            {{ $kwt->district ?? 'Tangerang' }}
                        </span>

                        <h3>{{ $kwt->name }} →</h3>

                        <p>
                            {{ $kwt->cultivation_cycles_count ?? 0 }}
                            siklus tanam tercatat.
                        </p>
                    </div>
                </a>
            @empty
                <p class="empty">
                    Belum ada KWT aktif.
                </p>
            @endforelse
        </div>
    </section>

    <section class="section soft-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">PRODUK SIAP JUAL</span>
                <h2>Hasil dari kebun kita.</h2>
            </div>

            <a href="{{ url('/produk-olahan') }}" class="text-link">
                Semua produk →
            </a>
        </div>

        <div class="grid-3">
            @forelse($products as $product)
                <a
                    class="kwt-card home-product-card"
                    href="{{ url('/produk-olahan/' . $product->id) }}"
                >
                    <img src="{{ \App\Support\ProductImage::url($product->name, $product->image_path) }}" alt="{{ $product->name }}" loading="lazy">
                    <div>
                        <small>
                            {{ $product->kwt?->name ?? 'Koperasi' }}
                        </small>

                        <h3>{{ $product->name }}</h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit($product->description, 85) }}
                        </p>

                        @if($product->price !== null)
                            <strong>
                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                / {{ $product->unit }}
                            </strong>
                        @endif
                    </div>

                    <span>→</span>
                </a>
            @empty
                <p class="empty">
                    Belum ada produk siap jual.
                </p>
            @endforelse
        </div>
    </section>

    <section class="cta-band">
        <div>
            <h2>
                Dekat dengan kebun,
                dekat dengan kebaikan.
            </h2>

            <p>
                Butuh hasil kebun atau ingin mengenal KWT lebih dekat?
                Mari berbincang dengan koperasi.
            </p>
        </div>

        <a href="{{ url('/kontak') }}" class="btn btn-cream">
            Hubungi Koperasi →
        </a>
    </section>
</x-layout>
