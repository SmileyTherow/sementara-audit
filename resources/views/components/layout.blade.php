@props([
    'title' => 'Dari kebun, untuk Tangerang',
    'admin' => false,
])

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="description"
        content="{{ $siteSettings['site_description'] ?? 'Budidaya, panen, dan cerita Kelompok Wanita Tani Kota Tangerang.' }}"
    >

    <title>
        {{ $title }} · {{ $siteSettings['site_name'] ?? 'KWT Tangerang' }}
    </title>

    @if($siteSettings['site_favicon'] ?? null)
        <link
            rel="icon"
            href="{{ Storage::disk('public')->url($siteSettings['site_favicon']) }}"
        >
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $admin ? 'admin-body' : '' }}">
    <a href="#main" class="skip-link">
        Langsung ke konten
    </a>

    @if($admin)
        <button
            class="drawer-toggle btn"
            aria-controls="sidebar"
            aria-expanded="false"
            data-menu="sidebar"
        >
            ☰ Menu
        </button>

        <aside id="sidebar" class="sidebar">
            <a href="{{ url('/admin') }}" class="brand">
                <span class="brand-icon">❧</span>

                <span>
                    KWT <b>Tangerang</b>
                    <small>RUANG PENGELOLA</small>
                </span>
            </a>

            <div class="sidebar-label">
                KEBUN & KOMUNITAS
            </div>

            @foreach([
                '/admin' => 'Ringkasan',
                '/admin/manage/kwts' => 'Kelompok Wanita Tani',
                '/admin/manage/commodities' => 'Master Komoditas',
                '/admin/manage/cycles' => 'Siklus Budidaya',
                '/admin/monitorings' => 'Monitoring Tanaman',
                '/admin/harvests' => 'Panen & Ketersediaan',
                '/admin/manage/products' => 'Produk Olahan',
                '/admin/reports' => 'Laporan Panen',
            ] as $url => $label)
                <a
                    class="side-link {{ request()->url() === url($url) ? 'selected' : '' }}"
                    href="{{ url($url) }}"
                >
                    <span>◇</span>
                    {{ $label }}
                </a>
            @endforeach

            @can('manage-settings')
                <div class="sidebar-label">
                    PENGATURAN
                </div>

                <a
                    class="side-link"
                    href="{{ url('/admin/settings') }}"
                >
                    Pengaturan Koperasi
                </a>

                <a
                    class="side-link"
                    href="{{ url('/admin/manage/commodity-categories') }}"
                >
                    Kategori Komoditas
                </a>

                <a
                    class="side-link"
                    href="{{ url('/admin/manage/product-categories') }}"
                >
                    Kategori Olahan
                </a>
            @endcan

            @can('manage-users')
                <a
                    class="side-link"
                    href="{{ url('/admin/manage/users') }}"
                >
                    Pengguna & Hak Akses
                </a>
            @endcan

            <div class="sidebar-bottom">
                <a href="{{ url('/') }}">
                    ↗ Lihat website publik
                </a>

                <a href="{{ url('/admin/account') }}">
                    {{ auth()->user()->name }}
                </a>

                <small>
                    @if(auth()->user()->isSuperAdmin())
                        Super Admin
                    @elseif(auth()->user()->isOperatorKwt())
                        Operator KWT
                    @elseif(auth()->user()->isKoperasi())
                        Koperasi
                    @else
                        Pengguna
                    @endif
                </small>

                <form action="{{ url('/logout') }}" method="post">
                    @csrf

                    <button class="text-button" type="submit">
                        Keluar akun →
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-shell">
            <header class="admin-header">
                <span>Koperasi KWT Kota Tangerang</span>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </header>

            <main id="main" class="admin-main">
    @else
        <div class="topbar">
            <span>Tumbuh bersama, sejahtera bersama.</span>
            <span>Forum Kelompok Wanita Tani · Kota Tangerang</span>
        </div>

        <header class="public-header">
            <a href="{{ url('/') }}" class="brand">
                @if($siteSettings['site_logo'] ?? null)
                    <img
                        class="brand-icon"
                        src="{{ Storage::disk('public')->url($siteSettings['site_logo']) }}"
                        alt="Logo koperasi"
                    >
                @else
                    <span class="brand-icon">❧</span>
                @endif

                <span>
                    {{ $siteSettings['site_name'] ?? 'KWT Tangerang' }}
                    <small>DARI KEBUN, UNTUK KITA</small>
                </span>
            </a>

            <button
                class="menu-toggle"
                aria-expanded="false"
                aria-controls="public-nav"
                data-menu="public-nav"
            >
                ☰
                <span class="sr-only">Menu</span>
            </button>

            <nav id="public-nav" class="public-nav">
                @foreach([
                    '/' => 'Beranda',
                    '/tentang' => 'Tentang',
                    '/kwt' => 'Jelajahi KWT',
                    '/komoditas' => 'Komoditas',
                    '/jadwal-panen' => 'Jadwal Panen',
                    '/produk-olahan' => 'Produk Olahan',
                    '/kontak' => 'Kontak',
                ] as $url => $label)
                    @if($url === '/komoditas')
                        <details class="nav-dropdown">
                            <summary>Komoditas</summary>

                            <div>
                                <a href="{{ url('/komoditas') }}">
                                    Semua Komoditas
                                </a>

                                <a href="{{ url('/sedang-ditanam') }}">
                                    Sedang Ditanam
                                </a>

                                <a href="{{ url('/segera-panen') }}">
                                    Segera Panen
                                </a>

                                <a href="{{ url('/ready') }}">
                                    Ready
                                </a>

                                <a href="{{ url('/komoditas?status=Pre-Order') }}">
                                    Pre-Order
                                </a>
                            </div>
                        </details>
                    @else
                        <a
                            href="{{ url($url) }}"
                            class="{{ request()->url() === url($url) ? 'current' : '' }}"
                        >
                            {{ $label }}
                        </a>
                    @endif
                @endforeach

                <a
                    class="btn btn-small"
                    href="{{ auth()->check() ? url('/admin') : url('/login') }}"
                >
                    {{ auth()->check() ? 'Dashboard' : 'Masuk Pengelola' }} ↗
                </a>
            </nav>
        </header>

        <main id="main">
    @endif

    @if(session('success'))
        <div class="toast" role="status">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-box" role="alert">
            <strong>Periksa kembali isian Anda.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}

    </main>

    @if($admin)
        </div>
    @else
        <footer>
            <div class="footer-grid">
                <div>
                    <a href="{{ url('/') }}" class="brand">
                        <span class="brand-icon">❧</span>
                        <span>KWT <b>Tangerang</b></span>
                    </a>

                    <p>
                        Merawat kebun. Menguatkan perempuan.<br>
                        Menumbuhkan kebaikan untuk kota.
                    </p>
                </div>

                <div>
                    <strong>Jelajahi</strong>
                    <a href="{{ url('/kwt') }}">
                        Kelompok Wanita Tani
                    </a>
                    <a href="{{ url('/produk-olahan') }}">
                        Produk Olahan
                    </a>
                    <a href="{{ url('/jadwal-panen') }}">
                        Jadwal Panen
                    </a>
                </div>

                <div>
                    <strong>Informasi</strong>
                    <a href="{{ url('/tentang') }}">
                        Tentang Kami
                    </a>
                    <a href="{{ url('/kontak') }}">
                        Hubungi Koperasi
                    </a>
                    <a href="{{ url('/login') }}">
                        Ruang Pengelola
                    </a>
                </div>

                <div>
                    <strong>Mari terhubung</strong>

                    <p>
                        {{ $siteSettings['cooperative_address'] ?? 'Kota Tangerang, Banten' }}
                    </p>

                    <p>
                        {{ $siteSettings['cooperative_email'] ?? '' }}
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                © {{ date('Y') }}
                {{ $siteSettings['cooperative_name'] ?? 'Forum KWT Kota Tangerang' }}

                <span>
                    Ditanam dengan cinta, tumbuh bersama.
                </span>
            </div>
        </footer>
    @endif

    <dialog id="confirmation">
        <form method="dialog">
            <h2>Konfirmasi tindakan</h2>

            <p id="confirmation-text">
                Lanjutkan perubahan ini?
            </p>

            <div class="actions">
                <button value="cancel" class="btn btn-light">
                    Kembali
                </button>

                <button value="confirm" class="btn">
                    Ya, lanjutkan
                </button>
            </div>
        </form>
    </dialog>
</body>
</html>
