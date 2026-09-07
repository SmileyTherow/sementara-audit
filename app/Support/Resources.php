<?php

namespace App\Support;

use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\CultivationCycle;
use App\Models\Gallery;
use App\Models\Kwt;
use App\Models\Post;
use App\Models\ProcessedProduct;
use App\Models\ProcessedProductCategory;
use App\Models\User;

class Resources
{
    public const UNITS = 'kg,gram,buah,ikat,pcs,pot,liter';

    public static function all(): array
    {
        return [
            'kwts' => [Kwt::class, 'Kelompok Wanita Tani', [
                'code' => 'required|string|max:40', 'name' => 'required|string|max:150', 'description' => 'nullable|string|max:10000', 'address' => 'nullable|string|max:255', 'district' => 'required|string|max:100', 'village' => 'required|string|max:100', 'chairman_name' => 'nullable|string|max:150', 'whatsapp' => 'nullable|regex:/^[0-9+ -]{8,20}$/', 'email' => 'nullable|email|max:150', 'established_year' => 'nullable|integer|min:1900|max:2100', 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180', 'google_maps_url' => 'nullable|url:http,https|max:255', 'status' => 'required|in:active,inactive', 'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']],
            'commodities' => [Commodity::class, 'Master Komoditas', [
                'kwt_id' => 'required|integer|exists:kwts,id', 'category_id' => 'nullable|integer|exists:commodity_categories,id', 'name' => 'required|string|max:150', 'variety' => 'nullable|string|max:150', 'description' => 'nullable|string|max:10000', 'default_growing_days' => 'required|integer|min:1|max:3650', 'default_survival_rate' => 'required|numeric|between:0,100', 'default_expected_weight_per_plant' => 'required|numeric|between:0,100000', 'yield_unit' => 'required|in:'.self::UNITS, 'default_seed_reserve_percentage' => 'required|numeric|between:0,100', 'cultivation_method' => 'nullable|string|max:150', 'is_active' => 'required|boolean', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']],
            'cycles' => [CultivationCycle::class, 'Siklus Budidaya', [
                'commodity_id' => 'required|integer|exists:commodities,id', 'start_date' => 'required|date', 'expected_harvest_date' => 'nullable|date|after_or_equal:start_date', 'planting_holes' => 'nullable|integer|min:1|max:10000000', 'seed_quantity' => 'required_without:planting_holes|nullable|integer|min:1|max:10000000', 'planting_quantity' => 'nullable|integer|min:1|max:10000000', 'seed_unit' => 'required|string|max:30', 'seed_source' => 'nullable|string|max:255', 'seed_price' => 'nullable|numeric|between:0,100000000', 'reserve_seed_percentage' => 'nullable|numeric|between:0,100', 'target_survival_rate' => 'nullable|numeric|between:0,100', 'target_weight_per_plant' => 'nullable|numeric|between:0,100000', 'land_area' => 'nullable|numeric|min:0', 'land_area_unit' => 'nullable|string|max:30', 'location_name' => 'nullable|string|max:255', 'cultivation_method' => 'nullable|string|max:150', 'person_in_charge' => 'nullable|string|max:150', 'status' => 'required|in:draft,seedling,growing,near_harvest,harvested,completed,failed,cancelled', 'preorder_enabled' => 'required|boolean', 'status_reason' => 'nullable|string|max:2000', 'notes' => 'nullable|string|max:10000']],
            'products' => [ProcessedProduct::class, 'Produk Olahan', [
                'kwt_id' => 'required|integer|exists:kwts,id', 'category_id' => 'nullable|integer|exists:processed_product_categories,id', 'name' => 'required|string|max:150', 'description' => 'nullable|string|max:10000', 'price' => 'nullable|numeric|min:0', 'unit' => 'required|in:'.self::UNITS, 'availability_status' => 'required|in:available,limited,sold_out,not_available', 'available_quantity' => 'nullable|numeric|min:0', 'whatsapp_note' => 'nullable|string|max:1000', 'is_active' => 'required|boolean', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']],
            'posts' => [Post::class, 'Kegiatan & Berita', [
                'kwt_id' => 'nullable|integer|exists:kwts,id', 'title' => 'required|string|max:180', 'excerpt' => 'nullable|string|max:1000', 'content' => 'required|string|max:50000', 'status' => 'required|in:draft,published', 'published_at' => 'nullable|date', 'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']],
            'galleries' => [Gallery::class, 'Galeri', [
                'kwt_id' => 'nullable|integer|exists:kwts,id', 'title' => 'required|string|max:180', 'caption' => 'nullable|string|max:1000', 'published_at' => 'nullable|date', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']],
            'users' => [User::class, 'Pengguna', [
                'name' => 'required|string|max:150', 'email' => 'required|email|max:150', 'password' => 'nullable|string|min:10|max:100', 'role' => 'required|in:super_admin,admin_koperasi,admin_kwt', 'kwt_id' => 'nullable|required_if:role,admin_kwt|integer|exists:kwts,id', 'is_active' => 'required|boolean', 'can_manage_cultivation' => 'required|boolean']],
            'commodity-categories' => [CommodityCategory::class, 'Kategori Komoditas', ['name' => 'required|string|max:100']],
            'product-categories' => [ProcessedProductCategory::class, 'Kategori Produk Olahan', ['name' => 'required|string|max:100']],
        ];
    }

    public static function get(string $key): array
    {
        return self::all()[$key] ?? abort(404);
    }

    public static function label(string $field): string
    {
        return [
            'kwt_id' => 'Kelompok Wanita Tani', 'commodity_id' => 'Komoditas', 'category_id' => 'Kategori', 'code' => 'Kode KWT', 'name' => 'Nama', 'description' => 'Deskripsi', 'address' => 'Alamat', 'district' => 'Kecamatan', 'village' => 'Kelurahan', 'chairman_name' => 'Nama ketua', 'established_year' => 'Tahun berdiri', 'cover_image' => 'Foto sampul', 'image' => 'Foto', 'is_active' => 'Aktif', 'can_manage_cultivation' => 'Izin input budidaya (admin koperasi)',
            'default_growing_days' => 'Masa tanam (hari)', 'default_survival_rate' => 'Target hidup (%)', 'default_expected_weight_per_plant' => 'Hasil per tanaman', 'yield_unit' => 'Satuan hasil', 'default_seed_reserve_percentage' => 'Cadangan bibit (%)', 'variety' => 'Varietas', 'cultivation_method' => 'Metode budidaya', 'start_date' => 'Tanggal mulai', 'expected_harvest_date' => 'Estimasi tanggal panen', 'planting_holes' => 'Jumlah lubang tanam', 'seed_quantity' => 'Jumlah bibit (tanpa lubang tanam)', 'planting_quantity' => 'Bibit yang ditanam', 'seed_unit' => 'Satuan bibit', 'seed_source' => 'Sumber bibit', 'seed_price' => 'Harga per bibit (Rp)', 'reserve_seed_percentage' => 'Cadangan bibit (%)', 'target_survival_rate' => 'Target hidup (%)', 'target_weight_per_plant' => 'Target hasil per tanaman', 'land_area' => 'Luas lahan', 'land_area_unit' => 'Satuan lahan', 'location_name' => 'Lokasi kebun', 'person_in_charge' => 'Penanggung jawab', 'preorder_enabled' => 'Buka pre-order WhatsApp', 'status_reason' => 'Alasan perubahan status', 'notes' => 'Catatan', 'price' => 'Harga informasi (Rp)', 'unit' => 'Satuan', 'availability_status' => 'Status ketersediaan', 'available_quantity' => 'Jumlah tersedia', 'whatsapp_note' => 'Catatan WhatsApp', 'title' => 'Judul', 'excerpt' => 'Ringkasan', 'content' => 'Isi kegiatan', 'published_at' => 'Tanggal publikasi', 'caption' => 'Keterangan foto', 'role' => 'Peran',
        ][$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
