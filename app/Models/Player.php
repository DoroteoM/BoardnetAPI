<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $guarded = [];

    public function play()
    {
        return $this->belongsTo('App\Models\Play');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team');
    }
}
