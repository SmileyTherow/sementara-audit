<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'harvest_id',
        'product_name',
        'quantity',
        'unit',
        'quality',
        'price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }
}
