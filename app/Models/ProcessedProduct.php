<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path_2', 'image_path_3', 'image_path_4', 'image_path_5',
        'processed_product_category_id',
        'kwt_id',
        'name',
        'description',
        'image_path',
        'price',
        'stock',
        'unit',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(
            ProcessedProductCategory::class,
            'processed_product_category_id'
        );
    }

    public function kwt()
    {
        return $this->belongsTo(Kwt::class);
    }
}
