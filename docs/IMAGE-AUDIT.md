# Audit gambar — 14 Oktober 2026

Dokumen ini merekam kondisi **sebelum perbaikan**. Status akhir tersedia pada [ringkasan pembaruan](UPDATE-2026-10-14-15.md).

Lingkup: branch main pada deployment XAMPP `http://localhost/kwt/public/`. Pemeriksaan source, file, database, dan respons HTTP gambar pada 11 halaman publik. Audit ini tidak mengganti gambar atau mengubah fitur aplikasi. Penilaian gambar yang ideal di bawah merupakan rekomendasi; perilaku yang sudah tersedia disebut secara terpisah.

## Hasil terverifikasi

- Aset ilustrasi yang tersedia: `public/images/garden.svg` (5.085 byte).
- `public/favicon.ico` berukuran 0 byte, sehingga bukan ikon yang valid.
- Tidak ada file upload gambar di `storage/app/public` maupun `storage/app/private`.
- Enam komoditas: Cabai, Tomat, Kangkung, Sawi, Jagung, Terong. Model/schema/form komoditas belum menyediakan gambar katalog.
- Empat KWT: Mawar, Melati, Anggrek, Seroja. Model/schema/form KWT belum menyediakan foto profil kebun.
- Tiga produk: Cabai Segar KWT Mawar, Kangkung Segar KWT Melati, Sambal Olahan KWT. Semua `image_path` bernilai NULL.
- Foto monitoring: 0 record. Foto panen: 0 record.
- Tidak ada pengaturan `site_logo` dan `site_favicon` pada database lokal.

## Peta gambar yang seharusnya tampil

| Lokasi | Perilaku source saat ini | Gambar yang disarankan / diharapkan |
|---|---|---|
| Header publik | Logo dari settings jika ada; jika kosong, simbol teks | Logo resmi koperasi/KWT; simbol tetap boleh menjadi fallback |
| Ikon tab browser | Link hanya dibuat jika site_favicon ada | Favicon PNG yang valid; aset favicon.ico bawaan saat ini kosong |
| Hero beranda | garden.svg | Ilustrasi kebun yang tersedia sudah sesuai sebagai ilustrasi; foto komunitas merupakan pilihan editorial |
| Kartu komoditas di beranda, katalog, detail KWT | garden.svg tanpa memilih foto | Foto sesuai jenis komoditas; butuh fitur gambar komoditas terlebih dahulu |
| Detail komoditas | garden.svg | Foto komoditas yang sama dengan kartu katalog |
| Timeline detail komoditas | Loop foto monitoring, tetapi src tetap garden.svg | File foto monitoring asli yang ditandai publik dan memenuhi aturan akses |
| Kartu KWT di beranda dan daftar KWT | garden.svg | Foto kebun/kelompok masing-masing KWT; fitur foto KWT belum tersedia |
| Detail KWT | garden.svg | Foto profil kebun/kelompok yang sama dengan kartu KWT |
| Produk olahan di beranda | Kartu teks, tidak ada elemen img | Foto produk dapat ditambahkan untuk konsistensi katalog; bukan gambar yang gagal dimuat |
| Daftar dan detail produk olahan | image_path, fallback garden.svg | Foto cabai segar, kangkung segar, dan sambal sesuai record; upload sudah didukung |
| Tentang | /images/garden.svg | Ilustrasi kebun yang sudah tersedia atau foto komunitas; URL sekarang gagal |
| Kontak | /images/garden.svg | Ilustrasi kebun yang sudah tersedia; URL sekarang gagal |
| Login | /images/garden.svg | Ilustrasi kebun yang sudah tersedia; URL sekarang gagal |
| Admin form produk/pengaturan | Preview file dengan Storage::url | Gambar yang sudah diupload; tidak perlu muncul ketika field kosong |
| Admin detail siklus — monitoring | /foto/monitoring/{id} | Foto monitoring asli, publik/privat sesuai hak pengguna |
| Admin detail siklus — panen | /foto/harvest/{id} | Foto panen asli untuk pengguna berizin; endpoint sekarang tidak menjadikannya publik |
| Kegiatan | Template cover_image tersedia, route dinonaktifkan | Sampul kegiatan hanya diperlukan jika fitur diaktifkan kembali |
| Jadwal panen | Tidak memiliki elemen gambar | Tidak ada gambar wajib menurut implementasi sekarang |

## Pemeriksaan HTTP publik

| Halaman | Jumlah img | Hasil gambar |
|---|---:|---|
| / | 8 | 8 × garden.svg, HTTP 200 |
| /komoditas | 4 | 4 × garden.svg, HTTP 200 |
| /komoditas/1 | 1 | garden.svg, HTTP 200 |
| /kwt | 4 | 4 × garden.svg, HTTP 200 |
| /kwt/1 | 3 | 3 × garden.svg, HTTP 200 |
| /produk-olahan | 3 | 3 × garden.svg, HTTP 200 |
| /produk-olahan/1 | 1 | garden.svg, HTTP 200 |
| /tentang | 1 | /images/garden.svg, HTTP 404 |
| /kontak | 1 | /images/garden.svg, HTTP 404 |
| /login | 1 | /images/garden.svg, HTTP 404 |
| /jadwal-panen | 0 | Tidak ada img |

Seluruh halaman di atas memberi HTTP 200, tetapi halaman yang berhasil dibuka masih bisa mengandung gambar 404. Total 27 elemen gambar, 24 berhasil dan 3 gagal. Ini pemeriksaan HTTP, bukan penilaian visual layout browser.

## Masalah dan prioritas

1. **URL ilustrasi salah pada tiga halaman.** Path `/images/garden.svg` mengarah ke root localhost, sedangkan aset ada di `/kwt/public/images/garden.svg`. Gunakan asset() seperti halaman lain.
2. **URL upload tidak sesuai subfolder.** `Storage::url()` menggunakan default disk local. Template sebaiknya secara eksplisit memilih disk public; konfigurasi URL public saat ini juga `/storage`, belum mengikuti APP_URL `/kwt/public`. Masalah ini memengaruhi produk, logo, favicon, dan preview admin ketika upload tersedia.
3. **URL foto admin tidak sesuai subfolder.** `/foto/monitoring/{id}` dan `/foto/harvest/{id}` mengarah ke root localhost. Gunakan route/url helper.
4. **Timeline publik tidak memakai file asli dan belum memfilter is_public.** Saat ini gambar tetap ilustrasi, tetapi caption foto ikut ditampilkan. Sebelum menghubungkan file asli, filter foto publik beserta pemeriksaan siklus/KWT/komoditas agar caption dan file privat tidak dipublikasikan.
5. **Pemeriksaan foto publik menggunakan field lama.** CultivationController::photo memeriksa `kwt->status === 'active'`, sementara schema/model KWT saat ini memakai `is_active`. Foto publik berpotensi selalu dianggap privat meskipun flag is_public aktif.
6. **Belum ada foto katalog komoditas/KWT.** Perlu migration, model, form upload, penyimpanan, serta pemilihan URL yang konsisten. Jangan menampilkan dokumentasi privat sebagai fallback katalog.
7. **File upload belum tersedia.** Database/SQL hanya menyimpan path. Dokumen backup menyebut arsip upload lokal programmer; file tersebut harus dipindahkan terpisah jika ingin memulihkan foto lama. Folder upload dikecualikan oleh .gitignore.
8. **Fallback hanya menangani path kosong.** Produk dengan image_path terisi tetapi file hilang akan menjadi gambar rusak, bukan otomatis garden.svg.
9. **Logo/favicon belum diisi dan favicon bawaan kosong.** Logo memakai simbol fallback; belum ada identitas gambar resmi.

## Batas verifikasi

Upload produk, preview admin, foto monitoring/panen, dan logo/favicon diaudit dari source karena data/file terkait belum tersedia. Tidak dibuat data atau upload sementara untuk menguji alur tersebut. Template kegiatan tidak termasuk halaman aktif. Dokumentasi lama menyatakan seeder membuat foto ilustrasi; seeder main saat ini tidak memasukkan record foto, sesuai hasil database lokal.

Source utama: resources/views/components/commodity-card.blade.php; resources/views/public/{home,commodity,products,product,kwts,kwt,about,contact}.blade.php; resources/views/auth/login.blade.php; resources/views/components/{layout,field}.blade.php; resources/views/admin/cycle.blade.php; config/filesystems.php; app/Http/Controllers/{ResourceController,SettingController,CultivationController}.php; app/Support/Resources.php; database/seeders/ProcessedProductSeeder.php dan GrowthMonitoringSeeder.php.
