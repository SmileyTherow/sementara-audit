<?php

namespace App\Models;

use App\Services\ProductAvailabilityService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
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

    public function cycles()
    {
        return $this->hasMany(CultivationCycle::class);
    }

    public function availabilities()
    {
        return $this->hasMany(HarvestAvailability::class);
    }

    public function latestAvailability()
    {
        return $this->hasOne(HarvestAvailability::class)->latestOfMany();
    }

    public function scopeReady($query)
    {
        return $query->active()->whereHas('kwt', fn ($q) => $q->active())
            ->whereHas('latestAvailability', fn ($q) => $q->whereIn('availability_status', ['available', 'limited'])->where('available_quantity', '>', 0));
    }

    public function scopePubliclyActive($query)
    {
        return $query->active()->whereHas('kwt', fn ($q) => $q->active())
            ->where(fn ($q) => $q->whereHas('cycles', fn ($c) => $c->active())
                ->orWhereHas('latestAvailability', fn ($a) => $a->whereIn('availability_status', ['available', 'limited'])->where('available_quantity', '>', 0)));
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getPublicStateAttribute()
    {
        return app(ProductAvailabilityService::class)->state($this);
    }
}
