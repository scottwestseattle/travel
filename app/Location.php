<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Base
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
        return $this->belongsToMany('App\Activity')->withTimestamps();
    }
	
    //
    // The entries that belong to the location
    //
    public function entries()
    {
        return $this->belongsToMany('App\Entry')->withTimestamps();
    }
	
}
