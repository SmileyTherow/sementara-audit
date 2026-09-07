<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
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
}
