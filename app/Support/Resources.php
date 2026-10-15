<?php

namespace App\Support;

use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\CultivationCycle;
use App\Models\Kwt;
use App\Models\ProcessedProduct;
use App\Models\ProcessedProductCategory;
use App\Models\User;

class Resources
{
    public const UNITS = 'kg,gram,buah,ikat,pcs,pot,liter';

    public static function all(): array
    {
        return [
            'kwts' => [
                Kwt::class,
                'Kelompok Wanita Tani',
                [
                    'code' => 'required|string|max:50',
                    'name' => 'required|string|max:150',
                    'leader_name' => 'nullable|string|max:150',
                    'phone' => 'nullable|string|max:30',
                    'address' => 'nullable|string|max:255',
                    'village' => 'nullable|string|max:100',
                    'district' => 'nullable|string|max:100',
                    'city' => 'nullable|string|max:100',
                    'province' => 'nullable|string|max:100',
                    'latitude' => 'nullable|numeric|between:-90,90',
                    'longitude' => 'nullable|numeric|between:-180,180',
                    'is_active' => 'required|boolean',
                ],
            ],

            'commodities' => [
                Commodity::class,
                'Master Komoditas',
                [
                    'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_5' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'commodity_category_id' => 'required|integer|exists:commodity_categories,id',
                    'code' => 'nullable|string|max:50',
                    'name' => 'required|string|max:150',
                    'default_unit' => 'nullable|string|max:50',
                    'default_growth_days' => 'nullable|integer|min:1|max:3650',
                    'default_harvest_days' => 'nullable|integer|min:1|max:3650',
                    'description' => 'nullable|string|max:10000',
                    'is_active' => 'required|boolean',
                ],
            ],

            'cycles' => [
                CultivationCycle::class,
                'Siklus Budidaya',
                [
                    'kwt_id' => 'required|integer|exists:kwts,id',
                    'commodity_id' => 'required|integer|exists:commodities,id',
                    'code' => 'required|string|max:50',
                    'planting_date' => 'required|date',
                    'estimated_harvest_date' => 'nullable|date|after_or_equal:planting_date',
                    'actual_harvest_date' => 'nullable|date|after_or_equal:planting_date',
                    'planting_area' => 'nullable|numeric|min:0',
                    'seed_quantity' => 'nullable|numeric|min:0',
                    'target_quantity' => 'nullable|numeric|min:0',
                    'target_unit' => 'nullable|string|max:50',
                    'status' => 'required|in:planned,planted,growing,ready_to_harvest,harvested,completed,cancelled',
                    'notes' => 'nullable|string|max:10000',
                ],
            ],

            'products' => [
                ProcessedProduct::class,
                'Produk Olahan',
                [
                    'processed_product_category_id' => 'required|integer|exists:processed_product_categories,id',
                    'kwt_id' => 'nullable|integer|exists:kwts,id',
                    'name' => 'required|string|max:150',
                    'description' => 'nullable|string|max:10000',
                    'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'image_path_5' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                    'price' => 'nullable|numeric|min:0',
                    'stock' => 'nullable|numeric|min:0',
                    'unit' => 'nullable|string|max:50',
                    'is_available' => 'required|boolean',
                ],
            ],

            'users' => [
                User::class,
                'Pengguna',
                [
                    'name' => 'required|string|max:150',
                    'email' => 'required|email|max:150',
                    'phone' => 'nullable|string|max:30',
                    'password' => 'nullable|string|min:8|max:255',
                    'kwt_id' => 'nullable|integer|exists:kwts,id',
                    'is_active' => 'required|boolean',
                    'role' => 'required|in:super_admin,operator_kwt,koperasi',
                ],
            ],

            'commodity-categories' => [
                CommodityCategory::class,
                'Kategori Komoditas',
                [
                    'name' => 'required|string|max:100',
                ],
            ],

            'product-categories' => [
                ProcessedProductCategory::class,
                'Kategori Produk Olahan',
                [
                    'name' => 'required|string|max:100',
                ],
            ],
        ];
    }

    public static function get(string $key): array
    {
        return self::all()[$key] ?? abort(404);
    }

    public static function label(string $field): string
    {
        return [
            'code' => 'Kode',
            'name' => 'Nama',
            'leader_name' => 'Nama Ketua',
            'phone' => 'Nomor Telepon',
            'address' => 'Alamat',
            'village' => 'Kelurahan/Desa',
            'district' => 'Kecamatan',
            'city' => 'Kota',
            'province' => 'Provinsi',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'is_active' => 'Aktif',

            'commodity_category_id' => 'Kategori Komoditas',
            'processed_product_category_id' => 'Kategori Produk',
            'commodity_id' => 'Komoditas',
            'kwt_id' => 'KWT',

            'default_unit' => 'Satuan Default',
            'default_growth_days' => 'Hari Pertumbuhan Default',
            'default_harvest_days' => 'Hari Panen Default',
            'description' => 'Deskripsi',

            'planting_date' => 'Tanggal Tanam',
            'estimated_harvest_date' => 'Estimasi Panen',
            'actual_harvest_date' => 'Tanggal Panen Aktual',
            'planting_area' => 'Luas Tanam',
            'seed_quantity' => 'Jumlah Benih',
            'target_quantity' => 'Target Produksi',
            'target_unit' => 'Satuan Target',
            'status' => 'Status',
            'notes' => 'Catatan',

            'image_path' => 'Foto Utama',
            'image_path_2' => 'Foto 2',
            'image_path_3' => 'Foto 3',
            'image_path_4' => 'Foto 4',
            'image_path_5' => 'Foto 5',
            'price' => 'Harga',
            'stock' => 'Stok',
            'unit' => 'Satuan',
            'is_available' => 'Tersedia',

            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Peran',
        ][$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
