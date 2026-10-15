<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image_path', 'image_path_2', 'image_path_3', 'image_path_4', 'image_path_5',
        'commodity_category_id',
        'code',
        'name',
        'default_unit',
        'default_growth_days',
        'default_harvest_days',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_growth_days' => 'integer',
            'default_harvest_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = [
        'public_state',
    ];

    public function category()
    {
        return $this->belongsTo(
            CommodityCategory::class,
            'commodity_category_id'
        );
    }

    public function cultivationCycles()
    {
        return $this->hasMany(CultivationCycle::class);
    }

    public function getPublicStateAttribute(): array
    {
        $cycle = $this->cultivationCycles
            ->whereIn('status', [
                'planned',
                'planted',
                'growing',
                'ready_to_harvest',
            ])
            ->sortBy('estimated_harvest_date')
            ->first();

        $availability = $this->cultivationCycles
            ->flatMap(fn ($cycle) => $cycle->harvests)
            ->flatMap(fn ($harvest) => $harvest->availability ? [$harvest->availability] : [])
            ->sortByDesc('id')
            ->first();

        $availableQuantity = $availability
            ? max(
                0,
                (float) $availability->available_quantity
                - (float) $availability->reserved_quantity
                - (float) $availability->sold_quantity
            )
            : 0;

        if (! $this->is_active) {
            $status = 'Tidak Aktif';
        } elseif ($availableQuantity > 0) {
            $status = 'Ready';
        } elseif ($cycle?->status === 'ready_to_harvest') {
            $status = 'Segera Panen';
        } elseif ($cycle) {
            $status = 'Sedang Ditanam';
        } else {
            $status = 'Habis';
        }

        return [
            'status' => $status,
            'quantity' => $availableQuantity,
            'cycle' => $cycle,
            'availability' => $availability,
        ];
    }
}
