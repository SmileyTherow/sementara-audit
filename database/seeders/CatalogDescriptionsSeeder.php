<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commodity;
use App\Models\ProcessedProduct;
use App\Support\CatalogDescriptions;

class CatalogDescriptionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Commodity::all() as $record) {
            $record->update(['description' => CatalogDescriptions::commodity($record->name)]);
        }
        foreach (ProcessedProduct::all() as $record) {
            $description = CatalogDescriptions::product($record->name);
            if ($description !== null) {
                $record->update(['description' => $description]);
            }
        }
    }
}
