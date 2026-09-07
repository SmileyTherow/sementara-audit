# Backup dan restore

Backup dibuat 7 September 2026 dari database MySQL lokal `kwt`.

| File | Isi | Lokasi |
|---|---|---|
| `kwt-full-2026-09-07.sql` | Backup lengkap, termasuk sesi dan audit | `backups/`, hanya lokal |
| `kwt-uploads-2026-09-07.zip` | Foto pada disk private dan public | `backups/`, hanya lokal |
| `kwt-demo-2026-09-07.sql` | Struktur lengkap dan data aplikasi demo | `database/backups/`, masuk GitHub |

Export GitHub berisi 4 KWT, 9 master komoditas, 6 akun demo, siklus, monitoring, panen, katalog, kegiatan, serta pengaturan koperasi. Data sesi login, reset password, cache, queue, dan audit tidak dimasukkan ke export GitHub; struktur tabelnya tetap disertakan. Akun yang diexport semuanya akun `@kwt.test`. Nomor WhatsApp koperasi tersimpan dalam pengaturan export.

## Restore SQL

Gunakan database tujuan baru/kosong. Dump mengganti tabel yang bernama sama jika diimpor ke database berisi data.

Masuk ke MySQL CLI:

```sh
mysql -u root -p
```

Kemudian:

```sql
CREATE DATABASE kwt_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kwt_restore;
SOURCE E:/laragon/www/kwt/database/backups/kwt-demo-2026-09-07.sql;
```

Untuk pemulihan lengkap lokal, gunakan `backups/kwt-full-2026-09-07.sql` sebagai sumber. Atur `.env` ke database hasil restore, lalu:

```sh
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
npm ci
npm run build
```

Untuk mengembalikan foto, ekstrak arsip upload ke `storage/app/` sehingga folder `private` dan `public` berada tepat di dalamnya. Dump SQL tidak mengandung isi file gambar. Export GitHub hanya memuat path dokumentasi demo; untuk foto yang sama gunakan arsip upload lokal.

Alternatif instalasi demo dari source tanpa dump:

```sh
php artisan migrate --seed
php artisan db:seed --class=KwtSayurDemoSeeder
```

Seeder utama menghasilkan foto ilustrasi monitoring. Seeder tambahan membuat KWT Sayur Sejahtera dengan Selada, Kol, dan Pakcoy. Isi WhatsApp melalui Pengaturan Koperasi setelah instalasi berbasis seeder.

## Pembaruan terakhir

- Detail komoditas menggunakan tata letak responsif dan kotak CTA WhatsApp koperasi pusat.
- Status Sedang Ditanam/Segera Panen dapat menanyakan pemesanan; pesan tidak menyatakan pre-order telah diterima.
- Ready dan Pre-Order menggunakan format pesan masing-masing.
- Pengunjung memasukkan jumlah; pesan membawa produk, KWT, jumlah, serta informasi panen yang relevan.
- Akun tambahan: `sayur@kwt.test`, password demo `KwtDemo2026!`.
- Pengujian source terbaru: **17 test, 191 assertion lulus**.

Folder `backups/`, `.env`, `vendor/`, `node_modules/`, data storage runtime, serta profil browser lokal dikecualikan dari Git.
