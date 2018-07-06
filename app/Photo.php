<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Photo extends Base
{
	static public function getIndex()
	{
		$q = '
			SELECT *
				, CONCAT(alt_text, " - ", location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", parent_id, "/") as photo_path
			FROM photos
			WHERE 1=1
			AND parent_id <> 0
			AND site_id = ?
			AND user_id = ?
			AND deleted_flag = 0
			AND location <> "" 
		';
		
		$records = DB::select($q, [SITE_ID, Auth::id()]);
		//dd($records);
		
		return $records;
	}

}
