# Dokumentasi pembaruan KWT Tangerang

Pembaruan 14–15 Oktober 2026 tersedia pada branch **update**, dikembangkan dari main `c2e2b49`. Folder dan database versi awal tetap terpisah.

Mulai dari dokumen berikut:

- [Ringkasan seluruh perubahan 14–15 Oktober](UPDATE-2026-10-14-15.md): kondisi akhir, daftar fitur, file utama, pengujian, dan batas verifikasi.
- [Panduan instalasi dan pembaruan branch update](DEPLOY-UPDATE.md): persyaratan, database, migration, seeder, aset, dan pengecekan.
- [Galeri lima foto](PRODUCT-GALLERY.md): penyimpanan, form admin, perilaku galeri, dan pemeriksaan upload.
- [Dua belas tambahan olahan rumah tangga](HOME-PROCESSED-PRODUCTS.md): produk, bahan utama, kemasan, dan seeder.
- [Audit gambar sebelum perbaikan](IMAGE-AUDIT.md): temuan awal; bukan status aplikasi saat ini.
- [Catatan tahap pembaruan katalog](CATALOG-UPDATE.md): proses awal penambahan foto dan peningkatan keterbacaan.
- [Arsitektur awal](IMPLEMENTATION.md) dan [backup/restore awal](BACKUP-RESTORE.md): dokumen bawaan repositori. Beberapa detailnya berasal dari schema/seeder lama; gunakan panduan branch update untuk pembaruan ini.

Data katalog dapat direkonstruksi melalui seeder dan aset yang disertakan. File .env, data storage runtime, dump database lokal, dependency, dan build tidak ikut dikirim. Tidak ada kebutuhan API gambar ketika aplikasi dijalankan: semua gambar katalog sudah menjadi file lokal.
