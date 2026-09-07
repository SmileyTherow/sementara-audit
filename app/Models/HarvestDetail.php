<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestDetail extends Model
{
    protected $guarded = ['id'];

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }
}
