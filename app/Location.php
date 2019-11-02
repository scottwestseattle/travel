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

		return $records;
	}		
	
    //
    // The entries that belong to the location
    //
    public function entries()
    {
        return $this->belongsToMany('App\Entry')->withTimestamps();
    }
	
	static public function getPlaces()
	{
		$records = null;
		
		$q = '
SELECT locations.id, locations.name as place, locations_parent.name as country1, country.name as country2
FROM locations
LEFT JOIN locations as locations_parent
	ON locations_parent.id = locations.parent_id AND locations_parent.deleted_flag = 0
LEFT JOIN locations as country
	ON country.id = locations_parent.parent_id AND country.deleted_flag = 0 AND country.location_type = 300
WHERE 1=1
AND locations.deleted_flag = 0
AND locations.location_type = 700
			;
			';

		$records = null;
		try {		
			$records = DB::select($q);
		}
		catch(\Exception $e)
		{
			// todo: log me
			//dump($e);
		}
		//dd($records);
		
		$locations = [];
		foreach($records as $record)
		{
			$l = $record->place;
			
			if (isset($record->country2))
				$l .= ', ' . $record->country2;
			else
				$l .= ', ' . $record->country1;
				
			$locations[$l] = $record->id;
		}
		
		ksort($locations);
		
		//dd($locations);
		
		return $locations;
	}
}
