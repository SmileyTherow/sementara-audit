<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\ProcessedProduct;
use App\Support\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CatalogPhotosSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([Commodity::class, ProcessedProduct::class] as $model) {
            foreach ($model::all() as $record) {
                $keywords = ['keripik pisang' => 'keripik-pisang', 'saus tomat' => 'saus-tomat'];
                foreach (['sambal', 'cabai', 'tomat', 'kangkung', 'sawi', 'jagung', 'terong', 'selada', 'mentimun', 'wortel', 'bayam', 'buncis', 'kol'] as $slug) {
                    $keywords[$slug] = $slug;
                }
                $slug = null;
                foreach ($keywords as $keyword => $candidate) {
                    if (str_contains(mb_strtolower($record->name), $keyword)) {
                        $slug = $candidate;
                        break;
                    }
                }
                if (!$slug) continue;
                $primary = basename(parse_url(ProductImage::url($record->name), PHP_URL_PATH));
                $sources = ['image_path' => 'images/products/' . $primary, 'image_path_2' => 'images/catalog-gallery/' . $slug . '-2.webp'];
                foreach ($sources as $field => $source) {
                    if ($record->$field || !is_file(public_path($source))) continue;
                    $target = 'catalog-demo/' . $record->getTable() . '/' . $record->id . '/' . basename($source);
                    Storage::disk('public')->put($target, file_get_contents(public_path($source)));
                    $record->$field = $target;
                }
                $record->save();
            }
        }
    }
}
