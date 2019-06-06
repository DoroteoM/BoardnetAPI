<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }

    public function teams()
    {
        return $this->hasMany('App\Models\Team');
    }
}
