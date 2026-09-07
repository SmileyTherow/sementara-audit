# KWT Kota Tangerang

Sistem Informasi Budidaya, Panen, dan Ketersediaan Produk Kelompok Wanita Tani Kota Tangerang.

Laravel 12 + MySQL + Blade + Tailwind CSS 4 + Vite. Zona waktu `Asia/Jakarta`, antarmuka Bahasa Indonesia. Pembelian dilakukan melalui WhatsApp koperasi; tidak ada keranjang, checkout, pembayaran, atau tabel pesanan pelanggan.

## Jalankan di komputer ini

```powershell
cd E:\laragon\www\kwt
php artisan serve --host=127.0.0.1 --port=8000
```

- Website: http://127.0.0.1:8000
- Login: http://127.0.0.1:8000/login
- Dashboard: http://127.0.0.1:8000/admin
- Database lokal: `kwt`, MySQL `127.0.0.1:3306`.
- WhatsApp koperasi sudah diatur pada database lokal: **0895333212323**. Normalisasi tautan: **62895333212323**. Dapat diubah melalui **Pengaturan Koperasi**.
- Gambar dan kegiatan seeder merupakan ilustrasi/data demo, bukan dokumentasi lapangan nyata.

## Akun demo

Semua akun memakai kata sandi **`KwtDemo2026!`**.

| Peran | Email | Lingkup |
|---|---|---|
| Super Admin | `superadmin@kwt.test` | Semua data, pengguna, izin khusus, pengaturan |
| Admin Koperasi | `koperasi@kwt.test` | Semua KWT dan laporan; KWT, kegiatan, ketersediaan, pengaturan |
| Admin KWT Mawar | `mawar@kwt.test` | Data KWT Mawar |
| Admin KWT Melati | `melati@kwt.test` | Data KWT Melati |
| Admin KWT Anggrek | `anggrek@kwt.test` | Data KWT Anggrek |

Admin Koperasi secara default tidak bisa menginput komoditas, budidaya, monitoring, panen, atau produk olahan. Super Admin dapat memberikan `can_manage_cultivation` melalui pengaturan pengguna. Admin KWT tidak bisa mengelola pengguna maupun pengaturan pusat.

## Persyaratan

- PHP 8.2+; diuji menggunakan PHP 8.3.30.
- Ekstensi PHP standar Laravel, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `xml`, `zip`; `gd` untuk gambar demo dan pengujian upload; `pdo_sqlite` untuk test default.
- Composer 2.
- MySQL 8; diuji menggunakan 8.4.3.
- Node.js 20+ dan npm.
- Direktori `storage` dan `bootstrap/cache` dapat ditulis oleh PHP.

## Instalasi baru

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Di PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp` bila diperlukan. Atur koneksi MySQL dalam `.env`:

```dotenv
APP_NAME="KWT Kota Tangerang"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kwt
DB_USERNAME=root
DB_PASSWORD=
APP_LOCALE=id
```

Buat database, lalu jalankan:

```sql
CREATE DATABASE kwt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Seeder instalasi baru sengaja membiarkan nomor WhatsApp kosong. Masuk sebagai Super Admin atau Admin Koperasi, lalu isi nomor resmi pada **Pengaturan Koperasi**. Nomor hanya disimpan di tabel `settings`, bukan di kode aplikasi. Instalasi lokal saat ini sudah diisi sesuai nomor dari pemilik proyek.

`php artisan db:seed` tidak menggandakan demo bila akun Super Admin demo sudah ada. Tanggal demo mengikuti tanggal saat seeder berjalan agar terdapat contoh sedang tumbuh, menjelang panen, dan ready.

Untuk membangun ulang database **demo yang boleh dihapus**:

```bash
php artisan migrate:fresh --seed
```

Perintah tersebut menghapus seluruh tabel beserta data dan pengaturan yang ada; gunakan `migrate` biasa untuk database yang sudah digunakan.

## Alur penggunaan

1. Super Admin/Admin Koperasi mengelola KWT dan pengaturan pusat. Super Admin membuat pengguna serta memasangkannya dengan KWT.
2. Admin KWT membuat **Master Komoditas**, satuan hasil, masa tanam, target hidup, dan hasil per tanaman.
3. Pilih **Siklus Budidaya → Mulai Budidaya**. Isi jumlah lubang tanam atau jumlah bibit langsung. Nilai target yang dikosongkan mengikuti master komoditas.
4. Buka detail siklus untuk menambah **Monitoring & Dokumentasi**. Foto dapat ditandai publik atau privat.
5. Aktifkan pre-order melalui **Ubah Status** jika masyarakat sudah boleh menghubungi koperasi sebelum panen. Perubahan status memerlukan alasan.
6. Gunakan **Catat Panen Baru** untuk setiap panen. Hasil layak otomatis memperbarui realisasi dan menambah ketersediaan. Grade/ukuran dan beberapa foto panen bersifat opsional.
7. Setelah penyaluran offline, isi jumlah aktual terbaru melalui **Perbarui Ketersediaan**. Riwayat pembaruan tetap tersimpan.
8. Buka **Laporan Panen**, pilih periode/KWT/wilayah/komoditas, lalu cetak atau export XLSX.
9. Masyarakat mengunjungi komoditas/produk olahan dan memasukkan jumlah untuk membentuk pesan WhatsApp. Input tersebut tidak disimpan sebagai transaksi.

Ekspektasi awal dikunci setelah siklus dibuat. Perubahan kondisi dicatat sebagai monitoring baru sehingga riwayat proyeksi tetap utuh. Siklus yang telah memiliki aktivitas tidak dapat dihapus; gunakan status selesai, gagal, atau batal dengan alasan.

## Pemeriksaan

```bash
php artisan test
php artisan view:cache
php artisan route:list --except-vendor
npm run build
```

Test default menggunakan SQLite in-memory, terpisah dari database aplikasi. Untuk mengulang suite pada MySQL, buat database **khusus pengujian**, lalu jalankan di PowerShell:

```powershell
$env:DB_CONNECTION='mysql'
$env:DB_DATABASE='kwt_testing'
php artisan test
Remove-Item Env:DB_CONNECTION
Remove-Item Env:DB_DATABASE
```

Suite menggunakan `RefreshDatabase`; jangan menunjuk database pengujian ke database aplikasi atau produksi.

Pemeriksaan browser opsional tersedia di `scripts/browser-check.mjs`. Script membutuhkan aplikasi di port 8000, Chrome headless dengan CDP di port 9223, dan akun demo. Script mengecek login, gambar, dan overflow pada desktop/tablet/mobile, serta menyimpan screenshot di `storage/app/screenshots`.

Hasil implementasi 7 September 2026:

| Pemeriksaan | Hasil |
|---|---|
| Laravel | 12.69.1 berjalan |
| MySQL migrate:fresh --seed | Berhasil, seluruh migration dan seeder |
| Test SQLite | 15 lulus, 165 assertion |
| Test MySQL | 15 lulus, 165 assertion |
| Blade compilation | Berhasil |
| Vite production build | Berhasil |
| Browser 1366 / 768 / 360 px | Tidak ada overflow halaman atau gambar gagal dimuat |
| Browser login | Berhasil menuju dashboard KWT |
| XLSX | File valid, cell angka numeric diperiksa dalam test |

## Deploy hosting

- Arahkan document root domain ke folder **`public/`**.
- Atur `.env` untuk database server, URL domain, `APP_ENV=production`, `APP_DEBUG=false`, dan HTTPS.
- Jalankan `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, `php artisan storage:link`, `npm ci`, `npm run build`, `php artisan config:cache`, `php artisan view:cache`.
- Aplikasi inti berjalan tanpa queue worker, Redis, scheduler, atau layanan pihak ketiga tambahan.
- Foto monitoring/panen disimpan di `storage/app/private`; foto publik hanya dilayani melalui controller setelah pemeriksaan izin. Foto profil dan katalog berada pada disk publik.
- Ganti kata sandi demo dan identitas/konten contoh sebelum digunakan oleh pengguna sebenarnya.

## Dokumentasi teknis

Lihat [docs/IMPLEMENTATION.md](docs/IMPLEMENTATION.md) untuk arsitektur, struktur folder, tabel, relasi, route, controller, service, policy, formula, status produk, laporan, hasil validasi, dan pengembangan lanjutan.
## Backup dan pembaruan terbaru

Lihat [panduan backup dan restore](docs/BACKUP-RESTORE.md). Dump demo tersedia di `database/backups/kwt-demo-2026-09-07.sql`; backup lengkap database dan arsip upload berada di folder lokal `backups/`.

Tambahan terbaru: KWT Sayur Sejahtera (Selada, Kol, Pakcoy), akun `sayur@kwt.test` dengan password `KwtDemo2026!`, CTA WhatsApp pusat dengan pesan otomatis untuk komoditas yang sedang ditanam, dan tata letak detail komoditas yang dirapikan. Jalankan `php artisan db:seed --class=KwtSayurDemoSeeder` untuk menambahkan contoh ini pada instalasi baru.

Hasil test terbaru setelah perubahan: **17 test, 191 assertion lulus**. Angka pada laporan implementasi awal adalah hasil pemeriksaan pada tahap sebelumnya.