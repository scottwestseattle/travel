<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    public function tags()
    {
		return $this->belongsToMany('App\Tag');
    }
	
    public function location()
    {
		return $this->belongsTo('App\Location');
    }	

	//
	// has many locations
	//
    public function locations()
    {
		return $this->belongsToMany('App\Location')->withTimestamps();
    }
	
}
