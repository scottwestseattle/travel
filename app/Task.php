<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
