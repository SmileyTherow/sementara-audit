<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CultivationCycle extends Model
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
        return ['start_date' => 'date', 'expected_harvest_date' => 'date', 'preorder_enabled' => 'boolean'];
    }

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function monitorings()
    {
        return $this->hasMany(GrowthMonitoring::class)->orderBy('monitoring_date')->orderBy('id');
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function scopeActive($q)
    {
        return $q->whereIn('status', ['seedling', 'growing', 'near_harvest']);
    }

    public function scopeGrowing($q)
    {
        return $q->whereIn('status', ['seedling', 'growing']);
    }

    public function scopeNearHarvest($q)
    {
        return $q->active()->where(fn ($q) => $q->where('status', 'near_harvest')->orWhereDate('expected_harvest_date', '<=', now()->addDays((int) Setting::valueOf('near_harvest_days', 7))));
    }
}
