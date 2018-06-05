<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Location extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    //
    // The activities that belong to the location
    //
    public function activities()
    {
        return $this->belongsToMany('App\Activity')->withTimestamps();
    }
	
	// get all locations that have at least one entry record
	static public function getPills()
	{
		$q = '
			SELECT locations.id, locations.name, count(locations.id) as count
			FROM locations
			JOIN entry_location entloc ON locations.id = entloc.location_id
			JOIN entries ON entloc.entry_id = entries.id 
				WHERE 1=1
				AND locations.deleted_flag = 0
				AND locations.popular_flag = 1
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
			GROUP BY 
				locations.id, locations.name
		';
		
		// get the list with the location included
		$records = DB::select($q, [ENTRY_TYPE_TOUR]);
		//dd($records);
		
		return $records;
	}		
	
    //
    // The entries that belong to the location
    //
    public function entries()
    {
        return $this->belongsToMany('App\Entry')->withTimestamps();
    }
	
}
