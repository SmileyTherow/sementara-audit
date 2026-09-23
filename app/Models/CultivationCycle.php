<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CultivationCycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kwt_id',
        'commodity_id',
        'code',
        'planting_date',
        'estimated_harvest_date',
        'actual_harvest_date',
        'planting_area',
        'seed_quantity',
        'target_quantity',
        'target_unit',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'planting_date' => 'date',
            'estimated_harvest_date' => 'date',
            'actual_harvest_date' => 'date',
            'planting_area' => 'decimal:2',
            'seed_quantity' => 'decimal:2',
            'target_quantity' => 'decimal:2',
        ];
    }

    public function kwt()
    {
        return $this->belongsTo(Kwt::class);
    }

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function growthMonitorings()
    {
        return $this->hasMany(
            GrowthMonitoring::class,
            'cultivation_cycle_id'
        );
    }

    /*
     * Alias yang dipakai oleh CultivationController.
     */
    public function monitorings()
    {
        return $this->hasMany(
            GrowthMonitoring::class,
            'cultivation_cycle_id'
        );
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function harvestAlerts()
    {
        return $this->hasMany(HarvestAlert::class);
    }
}