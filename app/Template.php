<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Template extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
