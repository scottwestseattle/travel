<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\Tools;

class Photo extends Base
{
    public function entries()
    {
        return $this->belongsToMany('App\Entry')->withTimestamps();
    }

	static public function getByParent($parent_id)
	{
		$q = '
			SELECT *
				, CONCAT(alt_text, " - ", location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", parent_id, "/") as photo_path
			FROM photos
			WHERE 1=1
			AND parent_id = ?
			AND deleted_flag = 0
		';
		
		$records = DB::select($q, [$parent_id]);
		
		return $records;
	}
	
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

	static protected function getCount($type)
	{
		$q = '
			SELECT count(photos.id) as count
			FROM photos
			JOIN entries ON entries.id = photos.parent_id AND entries.published_flag = 1 AND entries.approved_flag = 1 AND entries.deleted_flag = 0 AND entries.type_flag = ?
			WHERE 1=1
				AND photos.site_id = ?
				AND photos.deleted_flag = 0
		';
				
		// get the list with the location included
		$record = DB::select($q, [$type, SITE_ID]);
		
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

		$stats['photos_article'] = Photo::getCount(ENTRY_TYPE_ARTICLE);
		$stats['photos_blog'] = Photo::getCount(ENTRY_TYPE_BLOG);
		$stats['photos_post'] = Photo::getCount(ENTRY_TYPE_BLOG_ENTRY);
		$stats['photos_tour'] = Photo::getCount(ENTRY_TYPE_TOUR);
		$stats['photos_gallery'] = Photo::getCount(ENTRY_TYPE_GALLERY);
		
		$stats['sliders'] = Photo::getCountSliders();
		
		return $stats;
	}

    static public function getGalleryMenuOptions()
    {
		$array = [];

		$records = Entry::getEntriesByType(ENTRY_TYPE_GALLERY, /* approved = */ false, /* limit = */ 0, /* site_id = */ null, /* orderBy = */ ORDERBY_TITLE);

		if (isset($records) && count($records) > 0)
		{
			foreach($records as $record)
			{
				$array[$record->id] = $record->title;					
			}
		}			
					
		return $array;
	}
	
	static protected function clearMainPhoto($parentId)
	{	
		DB::table('photos')
            ->where('parent_id', $parentId)
            ->update(['main_flag' => 0]);
	}
	
	static protected function setDisplayDate($parentId, $displayDate)
	{	
		DB::table('photos')
            ->where('parent_id', $parentId)
            ->update(['display_date' => $displayDate]);
	}
	
	static protected function getNextPrev($parent_id, $id, $next = true)
	{
		$record = Photo::select()				 
			->where('photos.deleted_flag', 0)
            ->where(function ($query) {
                $query->where('photos.gallery_flag', 1)
                      ->orWhere('photos.parent_id', 0);
            })		
			->where('photos.parent_id', '=', $parent_id)			
			->where('photos.id', $next ? '>' : '<', $id)
			->orderByRaw('photos.id ' . ($next ? 'ASC' : 'DESC'))
			->first();
			
		if (!isset($record))
		{
			if ($next)
				$record = Photo::getFirst($parent_id, $next);
			else // prev
				$record = Photo::getLast($parent_id, $next);
		}
		
		$record = Photo::setPermalink($record);

		return $record;
	}

	static protected function getLast($parent_id)
	{
		$parent_id = intval($parent_id);
		
		$record = Photo::select()				 
			->where('photos.deleted_flag', 0)
			->where('photos.parent_id', '=', $parent_id)	
            ->where(function ($query) {
                $query->where('photos.gallery_flag', 1)
                      ->orWhere('photos.parent_id', 0);
            })		
			->orderByRaw('photos.id DESC')
			->first();
		
		$record = Photo::setPermalink($record);

		return $record;
	}
		
	static protected function getFirst($parent_id)
	{
		$parent_id = intval($parent_id);
		
		$record = Photo::select()				 
			->where('photos.deleted_flag', 0)
            ->where(function ($query) {
                $query->where('photos.gallery_flag', 1)
                      ->orWhere('photos.parent_id', 0);
            })
			->where('photos.parent_id', '=', $parent_id)			
			->orderByRaw('photos.id ASC')
			->first();
		
		$record = Photo::setPermalink($record);

		return $record;
	}
	
	static protected function get($id)
	{
		$id = intval($id);

		$record = Photo::select()				 
			->where('photos.deleted_flag', 0)
            ->where(function ($query) {
                $query->where('photos.gallery_flag', 1)
                      ->orWhere('photos.parent_id', 0);
            })		
			->where('photos.id', $id)			
			->first();
			
		$record = Photo::setPermalink($record);
		
		return $record;
	}
	
	static protected function setPermalink($record)
	{
		if (isset($record) && !isset($record->permalink))
			$record->permalink = basename($record->filename, '.jpg');
			
		return $record;
	}

	static protected function getLocationsFromPhotos($standardCountryNames)
	{		
		$q0 = '
			SELECT location FROM `photos` 
			WHERE 1=1
			AND location IS NOT NULL
			AND location != ""
			AND type_flag in (0,1)
			AND gallery_flag = 1
			AND deleted_flag = 0
			GROUP BY `parent_id`, location
			ORDER BY parent_id DESC
			;
		';

		$q = '
			SELECT location, YEAR(display_date) as year_display, YEAR(created_at) as year FROM `photos` 
			WHERE 1=1
			AND location IS NOT NULL
			AND location != ""
			AND type_flag in (0,1)
			AND deleted_flag = 0
			GROUP BY `parent_id`, YEAR(display_date), YEAR(created_at), location
			ORDER BY parent_id DESC
			;
		';
		
		$qTEST = '
			SELECT location, YEAR(created_at) as year FROM `photos` 
			WHERE 1=1
			AND location IS NOT NULL
			AND location != ""
			AND type_flag in (0,1) 
			AND deleted_flag = 0
			GROUP BY `parent_id`, YEAR(created_at), location
			ORDER BY YEAR(created_at) DESC
			;
		';
		
		$records = null;
		try 
		{		
			$records = DB::select($q);
		}
		catch(\Exception $e)
		{
			// todo: log me
			//dump($e);
			dump("error getting country list from photos");
		}

		$locations = [];
		if (isset($records))
		{
			foreach($records as $record)
			{
				//dump($record->location);
				$country = Tools::getCountryFromLocation($standardCountryNames, $record->location);
				if ($country == 'Washington')
				{
					// don't show it as a country
					//	dump($record);
				}
				else if (!array_key_exists($country, $locations))
				{
					$year = isset($record->year_display) ? $record->year_display : $record->year;
					$year = ($year >= 2010 ? $year : 1999); // put everything older than 2010 together in 1999
					$locations[$country] = [$country, $year];
				}
			}

			$locationsByYear = [];
			foreach($locations as $record)
			{
				//dump($record);
				$locationsByYear[$record[1]][] = $record[0];
			}
	
			krsort($locationsByYear);
		}
		
		//dump($locations);
		//dump($locationsByYear);
		
		return ['countries' => $locations, 'countriesByYear' => $locationsByYear];
	}
		
}
