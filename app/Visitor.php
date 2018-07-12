<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Visitor extends Base
{
    static public function getVisitorsToday()
    {		
		$month = intval(date("m"));
		$year = intval(date("Y"));
		$day = intval(date("d"));
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
