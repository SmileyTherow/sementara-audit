<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Harvest extends Model
{
    use SoftDeletes;

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

    protected function casts(): array
    {
        return ['harvest_date' => 'date'];
    }

    public function cycle()
    {
        return $this->belongsTo(CultivationCycle::class, 'cultivation_cycle_id');
    }

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function details()
    {
        return $this->hasMany(HarvestDetail::class);
    }

    public function photos()
    {
        return $this->hasMany(HarvestPhoto::class);
    }

    public function availabilities()
    {
        return $this->hasMany(HarvestAvailability::class);
    }

    public function scopeWithinPeriod($q, $start, $end)
    {
        return $q->whereBetween('harvest_date', [$start, $end]);
    }
}
