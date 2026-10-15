# Pembaruan katalog dan keterbacaan

Catatan tahap awal. Kondisi akhir dan branch pengiriman **update** dirangkum pada [seluruh pembaruan 14–15 Oktober](UPDATE-2026-10-14-15.md). Halaman kredit foto sudah dihapus, upload katalog sudah diuji, dan pemasangan ulang mengikuti [DEPLOY-UPDATE.md](DEPLOY-UPDATE.md).

Branch: `fix/gambar-katalog`, dibuat dari main c2e2b49. Worktree lokal: `C:/xampp/htdocs/kwt-update`. URL preview: http://localhost/kwt-update/public/. Database preview: `kwt_update`, salinan terpisah dari database kwt. Versi utama tetap di folder kwt.

## Perubahan

Pembaruan lanjutan: tiga gambar produk olahan diganti mockup kemasan AI dengan latar putih dan pencahayaan studio. Saus tomat memakai botol, keripik pisang pouch, sambal jar. Nama file: saus-tomat-packshot.webp, keripik-pisang-packshot.webp, sambal-packshot.webp. Gambar dibuat melalui imagegen pada 14 Oktober 2026. PNG sumber tersimpan di folder aset proyek, dan gambar referensi sebelumnya tetap tersedia. Kartu dan detail memakai object-fit: contain agar kemasan tidak terpotong. Footer dan halaman sumber gambar menjelaskan status mockup AI. Foto komoditas tetap menggunakan referensi.

- Foto referensi asli untuk cabai, tomat, kangkung, sawi, jagung, terong, dan sambal. Foto disimpan lokal dalam WebP dan tidak membutuhkan akses internet saat halaman dibuka. Sumber, pencipta, lisensi, dan perubahan ukuran tercatat di public/images/products/credits.json dan halaman /kredit-foto.
- Foto upload produk pada disk public memiliki prioritas. Jika file upload tidak tersedia, pemilihan referensi mengikuti nama produk. Jenis produk yang belum dipetakan tetap menggunakan ilustrasi kebun. Foto referensi tidak diklaim sebagai dokumentasi hasil panen KWT.
- Beranda menampilkan foto pada kartu produk; katalog dan detail menggunakan sumber yang konsisten.
- Satu keluarga font Segoe UI dengan fallback sans-serif. Isi utama 17 px, informasi pendukung umumnya 14 px, kontrol dan navigasi 16 px. Heading tetap lebih besar. Ukuran dan jarak kartu, formulir, detail, serta susunan layar kecil diperbarui.
- URL gambar, upload, navigasi, dan form yang sebelumnya menunjuk root localhost kini mengikuti base URL. Preview upload memakai disk public secara eksplisit.
- Kurung penutup match pada detail komoditas diperbaiki. Timeline mengacu ke endpoint foto dan memfilter foto publik. Endpoint menggunakan is_active pada KWT.

## Validasi

Ekspansi katalog: beranda dan /komoditas menampilkan 12 kartu. Daftar: Cabai, Tomat, Kangkung, Sawi, Jagung, Terong, Selada, Mentimun, Wortel, Bayam, Buncis, Kol. CatalogExpansionSeeder menambahkan enam master baru dan delapan siklus contoh untuk komoditas yang sebelumnya tidak tampil. Seeder tidak menggandakan data ketika diulang. Untuk database preview yang sudah ada, jalankan `php artisan db:seed --class=CatalogExpansionSeeder --force`. Seeder utama juga memanggil ekspansi pada instalasi baru. Tombol Lihat Semua Komoditas mengarah ke /komoditas melalui url helper. Mentimun dan bayam memakai gambar AI; sumber lainnya dicatat di credits.json.

Build Vite dan cache Blade berhasil. Enam belas halaman publik yang diperiksa merespons HTTP 200. Semua sumber gambar unik yang dirujuk halaman tersebut merespons HTTP 200. Login admin mencapai /kwt-update/public/admin. Website utama tetap HTTP 200. Tujuh foto diperiksa secara visual sebagai file gambar.

Browser UI tidak tersedia dalam sesi, sehingga tampilan akhir dan overflow pada setiap lebar layar belum diverifikasi secara visual. Foto monitoring/panen dan upload pengguna belum diuji karena tidak tersedia data fotonya. Perubahan ini belum di-push ke GitHub.

## Menjalankan pada lingkungan lain

Jalankan composer install --no-dev, npm ci, npm run build; buat .env dan APP_KEY sendiri. Atur APP_URL ke URL deployment serta database yang dituju. Jalankan migration sesuai prosedur lingkungan, storage:link, config:cache, dan view:cache. Jangan menyalin kredensial .env atau database preview ke produksi. Aset WebP dan credits.json ikut source branch, sehingga tidak memerlukan seeding path foto.
