<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use DateTime;

class Visitor extends Base
{
    static public function getCountryInfo()
    {
    	$info = [];
		$info['lastCountry'] = 'none';
		$info['newestCountry'] = 'none';
		$info['totalCountries'] = 0;
    	
		$record = Visitor::select('country', 'created_at')
			//->where('site_id', 1)
			->whereNotNull('country')
			->orderByRaw('id DESC')
			->first();

		$info['lastCountry'] = $record->country;

		$q = '
			SELECT country, max(created_at), count(id)
			FROM visitors
			WHERE 1
			AND country is not null
			GROUP BY country
		';
		
		$records = DB::select($q);
		//dd($records);
		$info['totalCountries'] = count($records);
  	
    	return $info;
	}
	
    static public function getVisitors($date = null)
    {		
		if (isset($date))
		{
			$date = DateTime::createFromFormat('Y-m-d', $date);
			
			$month = intval($date->format("m"));
			$year = intval($date->format("Y"));
			$day = intval($date->format("d"));
		}
		else
		{
			$month = intval(date("m"));
			$year = intval(date("Y"));
			$day = intval(date("d"));
		}

		$fromTime = ' 00:00:00';
		$toTime = ' 23:23:59';
		
		$fromDate = '' . $year . '-' . $month . '-' . $day . ' ' . $fromTime;
		$toDate = '' . $year . '-' . $month . '-' . $day . ' ' . $toTime;

		$q = '
			SELECT *
			FROM visitors 
			WHERE 1=1 
			AND site_id = ?
			AND deleted_flag = 0 
 			AND (created_at >= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s") AND created_at <= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s")) 
			ORDER BY id DESC 
		';
					
		$records = DB::select($q, [SITE_ID, $fromDate, $toDate]);
		
		return $records;
    }
}
