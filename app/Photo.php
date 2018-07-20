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
		
		return $records;
	}

	static protected function getCount()
	{
		$q = '
			SELECT count(photos.id) as count
			FROM photos
			JOIN entries ON entries.id = photos.parent_id AND entries.published_flag = 1 AND entries.approved_flag = 1 AND entries.deleted_flag = 0
			WHERE 1=1
				AND photos.site_id = ?
				AND photos.deleted_flag = 0
		';
				
		// get the list with the location included
		$record = DB::select($q, [SITE_ID]);
		
		return intval($record[0]->count);
	}
	
	static protected function getCountSliders()
	{
		$q = '
			SELECT count(photos.id) as count
			FROM photos
			WHERE 1=1
				AND photos.deleted_flag = 0
				AND (photos.parent_id = 0 OR photos.parent_id IS NULL)
				AND photos.site_id = ?
		';
				
		// get the list with the location included
		$record = DB::select($q, [SITE_ID]);
		
		return intval($record[0]->count);
	}
	
	static public function getStats()
	{
		$stats = [];
		
		$stats['photos'] = Photo::getCount();
		$stats['sliders'] = Photo::getCountSliders();
		
		return $stats;
	}

}
