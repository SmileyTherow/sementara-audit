<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'cultivation_cycle_id',
        'monitoring_date',
        'plant_height',
        'plant_condition',
        'health_status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'monitoring_date' => 'date',
            'plant_height' => 'decimal:2',
        ];
    }

    public function cultivationCycle()
    {
        return $this->belongsTo(CultivationCycle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function photos()
    {
        return $this->hasMany(GrowthMonitoringPhoto::class);
    }
}
