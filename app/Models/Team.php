<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public function play()
    {
        return $this->belongsTo('App\Models\Play');
    }
}
