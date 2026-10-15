# Instalasi branch update

## Persyaratan

PHP 8.2+ dan ekstensi Laravel, pdo_mysql, mbstring, fileinfo, zip, xml, serta gd. Composer 2, Node.js 20+, npm, MySQL/MariaDB. Folder storage dan bootstrap/cache harus dapat ditulis PHP. Lock file alat pengembangan Pint memerlukan PHP 8.3; pada PHP 8.2 gunakan composer install --no-dev untuk runtime aplikasi.

## Instalasi baru

```powershell
git clone --branch update https://github.com/dicyi-ship-it/kwt-tangerang.git kwt-update
cd kwt-update
composer install --no-dev --prefer-dist --optimize-autoloader
Copy-Item .env.example .env
php artisan key:generate
```

Atur APP_URL dan koneksi database pada .env milik lingkungan tersebut. Untuk XAMPP subfolder, contoh APP_URL adalah http://localhost/kwt-update/public. Buat database baru dengan charset utf8mb4, lalu isi DB_DATABASE, DB_HOST, DB_PORT, DB_USERNAME, dan DB_PASSWORD sesuai server. .env tidak disertakan di Git.

```powershell
php artisan migrate --seed --force
php artisan storage:link
npm.cmd ci
npm.cmd run build
php artisan config:cache
php artisan view:cache
```

Seeder membuat 12 komoditas, 15 produk olahan, relasi KWT, dan dua foto awal per record katalog. Foto disalin dari aset source ke storage/app/public. Migration tidak menghapus tabel yang sudah ada. Jangan memakai migrate:fresh pada database berisi data yang harus dipertahankan.

## Pembaruan database yang sudah ada

Backup database dan storage terlebih dahulu. Pasang source update, dependency, dan build, lalu jalankan migration biasa:

```powershell
php artisan migrate --force
php artisan storage:link
npm.cmd ci
npm.cmd run build
php artisan config:cache
php artisan view:cache
```

Untuk **database preview** yang ingin dilengkapi dengan isi katalog seperti lingkungan pengembangan, jalankan:

```powershell
php artisan db:seed --class=CatalogExpansionSeeder --force
php artisan db:seed --class=CatalogPhotosSeeder --force
php artisan db:seed --class=HomeProcessedProductsSeeder --force
php artisan db:seed --class=CatalogDescriptionsSeeder --force
```

CatalogDescriptionsSeeder menulis ulang deskripsi record katalog yang dikenali. Seeder utama juga memperbarui beberapa akun dan data awal, sehingga jangan menjalankan seluruh db:seed secara membabi buta pada database operasional. Foto yang sudah terisi tidak ditimpa oleh CatalogPhotosSeeder/HomeProcessedProductsSeeder. Data akun awal mengikuti UserSeeder; identitas produksi dikelola melalui admin.

## Web server dan upload

Pada hosting, document root harus menuju public/. Pada XAMPP, root .htaccess memberi redirect ke public/ dan menolak akses source dari web. Pastikan mod_rewrite dan AllowOverride aktif. APP_ENV/APP_DEBUG harus disesuaikan dengan lingkungan; gunakan APP_DEBUG=false pada layanan publik.

Lima foto maksimal 4 MB berarti request bisa memuat sekitar 20 MB. Atur upload_max_filesize minimal 4M, post_max_size di atas 20M, dan max_file_uploads minimal 5. XAMPP pengembangan memakai batas PHP 40M. Restart Apache setelah mengubah php.ini.

Konfigurasi filesystem memakai APP_URL untuk URL storage. Setelah mengubah APP_URL atau konfigurasi database, jalankan kembali config:cache. Aktifkan Apache dan MySQL, kemudian kunjungi /, /komoditas, /produk-olahan, /login, dan detail produk.

## Pemeriksaan

```powershell
php artisan migrate:status
php artisan view:cache
npm.cmd run build
node scripts/check-gallery.mjs
```

`php scripts/check-catalog-photos.php` hanya diizinkan untuk database preview bernama kwt_update. Script mengganti file sementara pada dua record untuk menguji upload, lalu mengembalikan data awal. Jangan menjalankannya pada database operasional bersama pengguna aktif.

## Yang tidak dikirim

.env, APP_KEY, kredensial database, sesi login, dependency vendor/node_modules, storage runtime, dump database lokal, dan hasil build tidak dikirim. Build dapat dihasilkan ulang; foto awal dapat diisi melalui seeder. Upload asli pengguna yang tidak termasuk aset source memerlukan pemindahan storage/app/public tersendiri.
