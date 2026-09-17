<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'harvest_id',
        'available_quantity',
        'reserved_quantity',
        'sold_quantity',
        'unit',
        'available_from',
        'available_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'available_quantity' => 'decimal:2',
            'reserved_quantity' => 'decimal:2',
            'sold_quantity' => 'decimal:2',
            'available_from' => 'date',
            'available_until' => 'date',
        ];
    }

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }
}
