<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    /**
     * The entries that belong to the tag
     */
    public function entries()
    {
        return $this->belongsToMany('App\Entry');
    }
}
