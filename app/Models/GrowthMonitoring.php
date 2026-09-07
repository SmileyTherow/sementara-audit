<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrowthMonitoring extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['monitoring_date' => 'date'];
    }

    public function cycle()
    {
        return $this->belongsTo(CultivationCycle::class, 'cultivation_cycle_id');
    }

    public function photos()
    {
        return $this->hasMany(GrowthMonitoringPhoto::class);
    }
}
