<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kwt extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function scopeForKwt($query, $id)
    {
        return $query->when($id, fn ($q) => $q->where('id', $id));
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->role === 'admin_kwt' ? $query->where('id', $user->kwt_id ?? 0) : $query;
    }

    public function commodities()
    {
        return $this->hasMany(Commodity::class);
    }

    public function cycles()
    {
        return $this->hasMany(CultivationCycle::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function processedProducts()
    {
        return $this->hasMany(ProcessedProduct::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
}
