<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Activity extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
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
	
	public function photos()
    {
		return $this->hasMany('App\Photo', 'parent_id');
    }

	static protected function get($parent_id)
	{
/*		
*/
	
		$q = '
			SELECT *
			FROM `activities`
			WHERE 1=1
				AND activities.deleted_flag = 0
				AND activities.parent_id = ?
			LIMIT 1
			;
		';
		
		// get the list with the location included
		$record = DB::select($q, [$parent_id]);
dd($parent_id);
//dd('id: ' . $parent_id . ', ' . $record);
		
		return $record;
	}	
	
}
