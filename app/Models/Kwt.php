<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kwt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kwts';

    protected $fillable = [
        'code',
        'slug',
        'name',
        'leader_name',
        'chairman_name',
        'phone',
        'address',
        'village',
        'district',
        'city',
        'province',
        'description',
        'established_year',
        'status',
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
            'established_year' => 'integer',
        ];
    }

    /**
     * Use slug for route model binding if present.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Ensure slug is present when setting name (helpful for seeders/tests).
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        if (empty($this->attributes['slug']) && !empty($value)) {
            $this->attributes['slug'] = Str::slug($value);
        }
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
