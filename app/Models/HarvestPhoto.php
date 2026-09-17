<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'harvest_id',
        'file_path',
        'caption',
    ];

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }
}
