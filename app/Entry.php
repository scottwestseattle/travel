<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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
	
	// get all locations that have at least one entry record
	static public function getAdminIndex()
	{
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
			AND entries.type_flag = ?
			AND entries.deleted_flag = 0
			AND (entries.published_flag = 0 OR entries.approved_flag = 0 OR entries.location_id = null)
		';
		
		$records = DB::select($q, [ENTRY_TYPE_TOUR]);
		//dd($records);
		
		return $records;
	}		
}
