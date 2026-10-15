<?php

namespace Database\Seeders;

use App\Models\ProcessedProduct;
use App\Models\Kwt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeProcessedProductsSeeder extends Seeder
{
    public function run(): void
    {
        $category = DB::table('processed_product_categories')->where('name', 'Produk Olahan')->value('id');
        if (!$category) return;
        $items = [
            ['keripik-bayam', 'Keripik Bayam', 'KWT-001', 10000, 'bungkus', 'Daun bayam kebun dibalut adonan tepung tipis lalu digoreng menjadi camilan renyah.', 'bayam-packshot.webp'],
            ['keripik-kangkung', 'Keripik Kangkung', 'KWT-002', 10000, 'bungkus', 'Daun kangkung dipilih dan diolah dengan adonan berbumbu sebagai camilan rumahan.', 'kangkung.webp'],
            ['keripik-terong', 'Keripik Terong', 'KWT-003', 12000, 'bungkus', 'Irisan terong tipis dibalut adonan ringan dan digoreng dalam jumlah kecil.', 'terong.webp'],
            ['stik-wortel', 'Stik Wortel', 'KWT-004', 12000, 'bungkus', 'Parutan wortel dicampur adonan gurih, dipotong menjadi stik, lalu digoreng.', 'wortel.webp'],
            ['stik-sawi', 'Stik Sawi', 'KWT-002', 10000, 'bungkus', 'Sawi hijau dicampurkan ke adonan stik gurih sebagai variasi olahan sayur kebun.', 'sawi.webp'],
            ['acar-mentimun-wortel', 'Acar Mentimun Wortel', 'KWT-001', 8000, 'wadah', 'Potongan mentimun dan wortel diolah menjadi acar rumahan untuk pelengkap makanan. Dibuat sesuai pesanan.', 'mentimun-packshot.webp'],
            ['acar-kol', 'Acar Kol', 'KWT-004', 8000, 'wadah', 'Irisan kol diolah menjadi acar sederhana untuk pelengkap lauk. Dibuat sesuai pesanan.', 'kol.webp'],
            ['selai-tomat', 'Selai Tomat', 'KWT-001', 15000, 'jar kecil', 'Tomat matang dimasak perlahan bersama gula menjadi selai rumahan untuk olesan roti.', 'tomat.webp'],
            ['manisan-tomat', 'Manisan Tomat', 'KWT-003', 12000, 'bungkus', 'Tomat matang diolah dengan gula lalu dikeringkan menjadi manisan kenyal.', 'tomat.webp'],
            ['marning-jagung', 'Marning Jagung', 'KWT-004', 10000, 'bungkus', 'Biji jagung tua dikeringkan, direbus, dan digoreng menjadi marning berbumbu sederhana.', 'jagung-packshot.webp'],
            ['puding-jagung', 'Puding Jagung', 'KWT-002', 6000, 'cup', 'Jagung manis diparut lalu dimasak bersama bahan puding menjadi kudapan dalam cup kecil. Dibuat sesuai pesanan.', 'jagung-packshot.webp'],
            ['sambal-terong', 'Sambal Terong', 'KWT-003', 12000, 'wadah', 'Terong dimasak bersama cabai dan bumbu dapur sebagai lauk rumahan. Dibuat sesuai pesanan.', 'terong.webp'],
        ];

        foreach ($items as [$slug, $name, $code, $price, $unit, $description, $ingredient]) {
            $kwt = Kwt::where('code', $code)->first();
            if (!$kwt) continue;
            $record = ProcessedProduct::firstOrNew(['name' => $name . ' ' . $kwt->name]);
            if (!$record->exists) {
                $record->fill([
                    'processed_product_category_id' => $category, 'kwt_id' => $kwt->id,
                    'description' => \App\Support\CatalogDescriptions::product($name) ?? $description,
                    'price' => $price, 'stock' => 10, 'unit' => $unit, 'is_available' => true,
                ]);
            }
            foreach (['image_path' => 'images/home-products/' . $slug . '.webp', 'image_path_2' => 'images/products/' . $ingredient] as $field => $source) {
                if ($record->$field || !is_file(public_path($source))) continue;
                $target = 'home-products/' . $slug . '/' . ($field === 'image_path' ? 'utama' : 'bahan-utama') . '.webp';
                Storage::disk('public')->put($target, file_get_contents(public_path($source)));
                $record->$field = $target;
            }
            $record->save();
        }
    }
}
