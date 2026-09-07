<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];

    public static function valueOf(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
