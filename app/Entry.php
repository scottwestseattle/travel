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
			AND entries.site_id = ?
			AND entries.type_flag = ?
			AND entries.deleted_flag = 0
			AND (entries.published_flag = 0 OR entries.approved_flag = 0 OR entries.location_id = null)
		';
		
		$records = DB::select($q, [SITE_ID, ENTRY_TYPE_TOUR]);
		//dd($records);
		
		return $records;
	}

	// get all entries except tours
	static public function getEntries($approved_flag = false)
	{
		$q = '
			SELECT entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink,
				count(photos.id) as photo_count
			FROM entries
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			WHERE 1=1
			AND entries.site_id = ?
			AND entries.deleted_flag = 0
			AND entries.type_flag <> ?
			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink
			ORDER BY entries.published_flag ASC, entries.approved_flag ASC, entries.display_date ASC, entries.id DESC
		';
				
		$records = DB::select($q, [SITE_ID, ENTRY_TYPE_TOUR]);
		
		return $records;
	}
	
	// get all entries for specified type
	static public function getEntriesByType($type_flag, $approved_flag = true)
	{
		if (!isset($type_flag))
			return(Entry::getEntries($approved_flag));
		
		$q = '
			SELECT entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink, entries.display_date 
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				, count(photos.id) as photo_count				
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			WHERE 1=1
			AND entries.site_id = ?
			AND entries.deleted_flag = 0
			AND entries.type_flag = ?
		';
		
		if ($approved_flag)
			$q .= ' AND entries.published_flag = 1 AND entries.approved_flag = 1 ';
		
		$q .= '
			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink, entries.display_date 
				, photo, photo_title, photo_path
			ORDER BY entries.published_flag ASC, entries.approved_flag ASC, entries.display_date ASC, entries.id DESC
		';
				
		$records = DB::select($q, [SITE_ID, $type_flag]);
		
		return $records;
	}
	
	static public function getBlogIndex()
	{
		$q = '
			SELECT entries.id, entries.title, entries.description, entries.permalink
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				, count(posts.id) as post_count 
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 AND photo_main.site_id = ?
			LEFT JOIN entries as posts
				ON posts.parent_id = entries.id AND posts.deleted_flag = 0 AND posts.published_flag = 1 AND posts.approved_flag = 1
			WHERE 1=1
				AND entries.site_id = ?
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
			GROUP BY 
				entries.id, entries.title, entries.description, entries.permalink, photo, photo_title, photo_path
			ORDER BY entries.id DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [SITE_ID, SITE_ID, ENTRY_TYPE_BLOG]);
		
		return $records;
	}
	
	static public function getLatestBlogPosts($limit)
	{
		$q = '
			SELECT entries.id, entries.title, entries.description, entries.permalink, entries.display_date
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				, blogs.title as blog_title, blogs.id as blog_id
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 AND photo_main.site_id = ?
			JOIN entries as blogs
				ON blogs.id = entries.parent_id AND blogs.deleted_flag = 0 AND blogs.published_flag = 1 AND blogs.approved_flag = 1
			WHERE 1=1
				AND entries.site_id = ?
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
			GROUP BY 
				entries.id, entries.title, entries.description, entries.permalink, entries.display_date, photo, photo_title, photo_path, blog_title, blog_id
			ORDER BY entries.id DESC 
			LIMIT ?
		';
		
		// get the list with the location included
		$records = DB::select($q, [SITE_ID, SITE_ID, ENTRY_TYPE_BLOG_ENTRY, intval($limit)]);
		
		return $records;
	}	

	static public function getBlogEntriesIndexAdmin($pending = false)
	{
		$q = '
			SELECT entries.id, entries.title, entries.description, entries.permalink, entries.published_flag, entries.approved_flag 
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
			FROM entries
			JOIN entries as blogs
				ON blogs.id = entries.parent_id AND blogs.site_id = ? AND (blogs.published_flag = 1 AND blogs.approved_flag = 1)
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 AND photo_main.site_id = ?
			WHERE 1=1
				AND entries.site_id = ?
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND (entries.published_flag = ? 
					OR entries.approved_flag = ?) 
			GROUP BY 
				entries.id, entries.title, entries.description, entries.permalink, photo, photo_title, photo_path, entries.published_flag, entries.approved_flag 
			ORDER BY entries.id DESC
		';
		
		// get the list with the location included
		$pending = $pending ? 0 : 1; // flip pending true to 0
		$records = DB::select($q, [SITE_ID, SITE_ID, SITE_ID, ENTRY_TYPE_BLOG_ENTRY, $pending, $pending]);
		
		return $records;
	}

	static protected function getNextPrevBlogEntry($display_date, $parent_id, $next = true)
	{
		$record = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('published_flag', 1)
			->where('approved_flag', 1)
			->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
			->where('display_date', $next ? '>' : '<', $display_date)
			->where('parent_id', $parent_id)
			->orderByRaw('display_date ' . ($next ? 'ASC' : 'DESC '))
			->first();

		return $record;
	}
	
	static public function getAboutPage()
	{
		$q = '
			SELECT entries.id, entries.title, entries.description, entries.permalink, entries.published_flag, entries.approved_flag 
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 AND photo_main.site_id = ?
			WHERE 1=1
				AND entries.site_id = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1
				AND entries.approved_flag = 1
				AND entries.title = \'page-about\' 
			GROUP BY 
				entries.id, entries.title, entries.description, entries.permalink, photo, photo_title, photo_path, entries.published_flag, entries.approved_flag 
			LIMIT 1
		';
		
		$record = DB::select($q, [SITE_ID, SITE_ID]);
		
		return $record;
	}
	
	protected function getEntryCount($entry_type, $allSites)
	{
		$q = '
			SELECT count(entries.id) as count
			FROM entries
			WHERE 1=1
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
				AND entries.type_flag = ?
		';
		
		$q .= $allSites ? '' : ' AND entries.site_id = ' . SITE_ID . ' ';
		
		// get the list with the location included
		$record = DB::select($q, [$entry_type]);
		
		return intval($record[0]->count);
	}	
	
}
