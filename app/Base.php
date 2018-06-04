<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public function deleteSafe()
    {
		$this->deleted_flag = 1;
		$this->save();
    }	
}
