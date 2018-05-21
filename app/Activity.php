<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
	//
	// has many locations
	//
    public function locations()
    {
		return $this->belongsToMany('App\Location')->withTimestamps();
    }	
}
