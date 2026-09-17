<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'cultivation_cycle_id',
        'harvest_id',
        'alert_type',
        'scheduled_at',
        'sent_at',
        'channel',
        'recipient_user_id',
        'status',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function cultivationCycle()
    {
        return $this->belongsTo(
            CultivationCycle::class,
            'cultivation_cycle_id'
        );
    }

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }

    public function recipient()
    {
        return $this->belongsTo(
            User::class,
            'recipient_user_id'
        );
    }
}
