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
}
