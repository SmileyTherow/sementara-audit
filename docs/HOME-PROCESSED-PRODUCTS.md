# Tambahan 12 olahan rumah tangga

Branch pengiriman akhir: **update**. Deskripsi yang tampil telah diganti menjadi dua paragraf bahasa katalog, tanpa kata contoh/demo dan tanpa keterangan bahan foto kedua. Foto kedua tetap menunjukkan bahan utama. Ringkasan terakhir tersedia di [seluruh pembaruan](UPDATE-2026-10-14-15.md).

Versi preview pada branch fix/gambar-katalog menambahkan 12 contoh produk sehingga total menjadi 15 produk olahan. Data, harga, stok, dan pembagian KWT adalah demo, bukan konfirmasi produksi nyata. Foto utama tambahan dibuat dengan imagegen pada 15 Oktober 2026 untuk menunjukkan kemasan sederhana dan proses olahan skala rumah tangga, tanpa klaim izin, sertifikasi, atau masa simpan.

| Produk tambahan | Bahan hasil budidaya | Kemasan contoh |
|---|---|---|
| Keripik Bayam | Bayam | Pouch bening |
| Keripik Kangkung | Kangkung | Pouch bening |
| Keripik Terong | Terong | Pouch bening |
| Stik Wortel | Wortel | Pouch bening |
| Stik Sawi | Sawi | Pouch bening |
| Acar Mentimun Wortel | Mentimun dan wortel | Wadah kecil |
| Acar Kol | Kol | Wadah kecil |
| Selai Tomat | Tomat matang | Jar kecil |
| Manisan Tomat | Tomat matang | Pouch bening |
| Marning Jagung | Jagung tua/kering | Pouch bening |
| Puding Jagung | Jagung manis | Cup |
| Sambal Terong | Terong dan cabai | Wadah kecil |

Foto kedua menunjukkan bahan utama dan dijelaskan dalam deskripsi produk. Seluruh produk tetap mendukung lima slot foto. Stok contoh dibuat kecil; olahan basah seperti acar, puding, dan sambal terong dijelaskan sebagai dibuat sesuai pesanan. Identitas KWT tampil dari relasi database.

Seeder: `php artisan db:seed --class=HomeProcessedProductsSeeder --force`. Seeder utama juga memanggilnya pada instalasi baru. Pengulangan seeder tidak menggandakan record dan tidak menimpa foto yang sudah diisi. Aset PNG dan WebP berada di public/images/home-products; seeder menyalin foto ke disk public dan mengisi path database. Katalog menampilkan 15 produk per halaman dan menyediakan tautan pagination.

Versi utama di folder kwt dan database kwt tidak diubah.
