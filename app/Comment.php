<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Base
{
	// has one visitor
    public function visitor()
    {
		return $this->belongsTo('App\Visitor');
    }		
}
