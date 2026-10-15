# Galeri produk — audit dan pembaruan

Branch pengiriman akhir: **update**. Setelah penambahan olahan rumah tangga, totalnya menjadi 12 komoditas dan 15 produk olahan; setiap record mempunyai dua foto awal dan mendukung hingga lima slot. Lihat [ringkasan akhir](UPDATE-2026-10-14-15.md).

Branch fix/gambar-katalog, lingkungan kwt-update, database kwt_update. Versi utama tidak diubah.

Audit awal: processed_products memiliki satu image_path dan satu input upload; komoditas belum memiliki field gambar. Detail hanya memiliki satu img tanpa thumbnail. URL action form admin juga salah membentuk ekspresi record sehingga diperbaiki bersama fitur upload.

## Lima slot foto

- Foto Utama: image_path. Digunakan pada kartu beranda/katalog dan foto pertama detail.
- Foto 2–5: image_path_2 sampai image_path_5. Ditampilkan dalam galeri detail jika file tersedia.
- Kedua jenis produk memakai lima input yang sama. Maksimal 4 MB per file, format JPG/JPEG/PNG/WebP. Slot kosong mempertahankan foto lama. Mengunggah file baru mengganti slot terkait; file lama dibuang setelah penyimpanan database berhasil. Jika penyimpanan database gagal, file baru dibersihkan dan file lama dipertahankan.
- Tombol thumbnail mengganti foto besar, alt text, dan status aria-pressed. Nomor slot tetap mengikuti field apabila ada slot yang kosong. Gambar ditampilkan utuh dengan object-fit:contain.

## Foto contoh

12 komoditas dan 3 produk olahan masing-masing diisi Foto Utama dan Foto 2. Foto 3–5 tersedia untuk upload admin. Foto 2 dibuat dengan AI sebagai gambar katalog demo dengan komposisi/sudut berbeda. Kemasan produk olahan mengikuti gambar utama. Gambar tidak dimaksudkan sebagai dokumentasi lapangan atau kemasan produksi resmi.

Aset tambahan WebP berada di public/images/catalog-gallery, PNG sumber juga disimpan di sana. CatalogPhotosSeeder menyalin aset contoh ke disk public dengan path database. Seeder hanya mengisi slot kosong, sehingga upload pengguna tidak ditimpa. Jalankan migration lalu `php artisan db:seed --class=CatalogPhotosSeeder --force` untuk instalasi preview yang sudah ada; instalasi baru memanggil seeder ini dari DatabaseSeeder. Foto lama tetap didukung melalui image_path dan fallback referensi.

## Pemeriksaan

`php scripts/check-catalog-photos.php`: pemeriksaan lima slot untuk dua jenis produk, validasi tipe/ukuran, penyimpanan kelima upload, penggantian dan pembersihan file lama, galeri lima foto, serta mempertahankan slot ketika tidak diupload. Script hanya diizinkan pada database preview kwt_update dan mengembalikan record semula.

`node scripts/check-gallery.mjs`: perilaku thumbnail, pergantian alt text/aria-pressed, kembali ke foto utama, dan isolasi dua galeri. Ini uji JavaScript terpisah, bukan inspeksi browser.

Form admin diperiksa melalui sesi login: masing-masing lima input file dan action yang valid. Seluruh 15 halaman detail dan URL gambar galeri diperiksa melalui HTTP. Build Vite dan cache Blade berhasil. Browser visual belum tersedia untuk memeriksa layout akhir secara langsung.
