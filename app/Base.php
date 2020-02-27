<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Base extends Model
{
    public function deleteSafe()
    {
		$this->deleted_flag = 1;
		$this->save();
    }	
    
	static protected function isAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SITE_ADMIN);
	}	
}
