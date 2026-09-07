<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestAvailability extends Model
{
    protected $guarded = ['id'];

    public function kwt()
    {
        return $this->belongsTo(Kwt::class);
    }

    public function scopeForKwt($query, $id)
    {
        return $query->when($id, fn ($q) => $q->where('kwt_id', $id));
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->role === 'admin_kwt' ? $query->where('kwt_id', $user->kwt_id ?? 0) : $query;
    }

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function cycle()
    {
        return $this->belongsTo(CultivationCycle::class, 'cultivation_cycle_id');
    }
}
