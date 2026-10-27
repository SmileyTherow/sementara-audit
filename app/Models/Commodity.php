<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Commodity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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

    /**
     * Provide a query builder for HarvestAvailability records that belong
     * to this commodity via cultivation_cycles -> harvests -> harvest_availabilities.
     *
     * Note: This returns an Eloquent query builder, not a conventional Eloquent
     * relation object. It's intended for usages like:
     *   $commodity->availabilities()->latest('id')->first();
     *
     * Implementation detail:
     * - harvest_availabilities.harvest_id -> harvests.id
     * - harvests.cultivation_cycle_id -> cultivation_cycles.id
     * - cultivation_cycles.commodity_id -> commodities.id
     */
    public function availabilities(): Builder
    {
        // Use the HarvestAvailability model as base and join through harvests -> cultivation_cycles
        $availTable = (new \App\Models\HarvestAvailability)->getTable();
        $harvestTable = (new \App\Models\Harvest)->getTable();
        $cycleTable = (new \App\Models\CultivationCycle)->getTable();

        return \App\Models\HarvestAvailability::query()
            ->join($harvestTable, "{$harvestTable}.id", '=', "{$availTable}.harvest_id")
            ->join($cycleTable, "{$cycleTable}.id", '=', "{$harvestTable}.cultivation_cycle_id")
            ->where("{$cycleTable}.commodity_id", $this->id)
            ->select("{$availTable}.*");
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
