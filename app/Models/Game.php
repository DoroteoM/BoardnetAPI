<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    public function libraries()
    {
        return $this->hasMany('App\Models\Library');
    }
}
