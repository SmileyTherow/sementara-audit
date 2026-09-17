<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'cultivation_cycle_id',
        'harvest_date',
        'predicted_quantity',
        'realized_quantity',
        'variance_quantity',
        'quality_grade',
        'status',
        'notes',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'predicted_quantity' => 'decimal:2',
            'realized_quantity' => 'decimal:2',
            'variance_quantity' => 'decimal:2',
            'verified_at' => 'datetime',
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

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function details()
    {
        return $this->hasMany(HarvestDetail::class);
    }

    public function photos()
    {
        return $this->hasMany(HarvestPhoto::class);
    }

    public function availability()
    {
        return $this->hasOne(HarvestAvailability::class);
    }

    public function alerts()
    {
        return $this->hasMany(HarvestAlert::class);
    }
}
