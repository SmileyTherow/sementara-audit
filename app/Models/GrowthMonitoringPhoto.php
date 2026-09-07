<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrowthMonitoringPhoto extends Model
{
    protected $guarded = ['id'];

    public function monitoring()
    {
        return $this->belongsTo(GrowthMonitoring::class, 'growth_monitoring_id');
    }
}
