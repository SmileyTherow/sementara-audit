<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthMonitoringPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'growth_monitoring_id',
        'file_path',
        'caption',
    ];

    public function growthMonitoring()
    {
        return $this->belongsTo(GrowthMonitoring::class);
    }
}
