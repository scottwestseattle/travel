<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Photo extends Base
{
	// get all locations that have at least one entry record
	static public function getIndex()
	{
		$q = '
			SELECT *
			FROM photos
			WHERE 1=1
			AND site_id = ?
			AND user_id = ?
			AND deleted_flag = 0
		';
		
		$records = DB::select($q, [SITE_ID, Auth::id()]);
		//dd($records);
		
		return $records;
	}
}
