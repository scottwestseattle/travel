<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    //
    // The activities that belong to the location
    //
    public function activities()
    {
        return $this->belongsToMany('App\Activity');
    }
}
