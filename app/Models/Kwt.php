<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kwt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kwts';

    protected $fillable = [
        'code',
        'name',
        'leader_name',
        'phone',
        'address',
        'village',
        'district',
        'city',
        'province',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cultivationCycles()
    {
        return $this->hasMany(CultivationCycle::class);
    }

    public function processedProducts()
    {
        return $this->hasMany(ProcessedProduct::class);
    }
}
