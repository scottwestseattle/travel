<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App;

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

	// has one primary location
    public function location()
    {
		return $this->belongsTo('App\Location');
    }	

	// has many breadcrumb heirarchy locations
    public function locations()
    {
		return $this->belongsToMany('App\Location')->withTimestamps();
    }

	// has many photos
    public function photos()
    {
		return $this->belongsToMany('App\Photo')->withTimestamps();
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
	static public function getEntriesByType($type_flag, $approved_flag = true, $limit = 0, $site_id = null, $orderBy = ORDERBY_APPROVED)
	{
		if (!isset($type_flag))
			return(Entry::getEntries($approved_flag));
		
		// if site_id is set use it, otherwise use the old SITE_ID constant for backwards compatibility
		$site_id = isset($site_id) && $site_id != false ? $site_id : SITE_ID;
		
		$q = '
			SELECT entries.id as id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink, entries.display_date, entries.site_id  
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id) as photo_path
				, photo_main_gallery.filename as photo_gallery 
				, CONCAT(photo_main_gallery.alt_text, " - ", photo_main_gallery.location) as photo_title_gallery
				, CONCAT("' . PHOTO_ENTRY_PATH . '", photo_main_gallery.parent_id) as photo_path_gallery
				, count(photos.id) as photo_count
				, locations.name as location, locations.location_type as location_type
				, locations_parent.name as location_parent
				, blogs.title as blog_title, blogs.id as blog_id 
				, translations.language, translations.medium_col1, translations.medium_col2, translations.large_col1, translations.large_col2
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 
			LEFT JOIN photos as photo_main_gallery
				ON photo_main_gallery.id = entries.photo_id AND photo_main_gallery.deleted_flag = 0 
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			LEFT JOIN locations
				ON locations.id = entries.location_id AND locations.deleted_flag = 0
			LEFT JOIN locations as locations_parent
				ON locations_parent.id = locations.parent_id AND locations_parent.deleted_flag = 0
			LEFT JOIN entries as blogs
				ON blogs.id = entries.parent_id AND blogs.deleted_flag = 0 AND blogs.published_flag = 1 AND blogs.approved_flag = 1
			LEFT JOIN translations
				ON entries.id = translations.parent_id AND translations.language = ? AND translations.parent_table = ? 
			WHERE 1=1
			AND entries.deleted_flag = 0
			AND entries.type_flag = ?
			AND entries.site_id = ?
		';
				
		if ($approved_flag)
			$q .= ' AND entries.published_flag = 1 AND entries.approved_flag = 1 ';
		
		$q .= '
			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.permalink, entries.display_date, entries.site_id  
				, photo, photo_title, photo_path
				, photo_gallery, photo_title_gallery, photo_path_gallery
				, location, location_parent, location_type
				, blog_title, blog_id
				, translations.language, translations.medium_col1, translations.medium_col2, translations.large_col1, translations.large_col2 
		';
		
		$orderByPhrase = 'ORDER BY entries.published_flag ASC, entries.approved_flag ASC, entries.display_date DESC, entries.id DESC';
		
		switch($orderBy)
		{
			case ORDERBY_APPROVED:
				// already set above
				break;
			case ORDERBY_TITLE:
				$orderByPhrase = 'ORDER BY entries.title ASC';
				break;
			case ORDERBY_DATE:
				$orderByPhrase = 'ORDER BY entries.display_date DESC, entries.id DESC';
				break;
			default:
				// already set above
				break;
		}
		
		$q .= ' ' . $orderByPhrase . ' ';
		
		if ($limit > 0)
			$q .= ' LIMIT ' . $limit . ' ';

		$records = DB::select($q, [App::getLocale(), 'entries', $type_flag, $site_id]);
		
		return $records;
	}
	
	static public function get($permalink, $id = null)
	{
		if (isset($permalink)) // clean the permalink
			$permalink = preg_replace("/[^a-zA-Z0-9\-]/", "", $permalink);
		
		$id = intval($id); // clean the id
		
		$record = null;
		
		$q = '
			SELECT entries.id, entries.type_flag, entries.view_count, entries.permalink, entries.title, entries.description
				, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date
				, entries.photo_id, entries.parent_id, entries.site_id 
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				, photo_main_gallery.filename as photo_gallery 
				, CONCAT(photo_main_gallery.alt_text, " - ", photo_main_gallery.location) as photo_gallery_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", photo_main_gallery.parent_id, "/") as photo_gallery_path
				, count(photos.id) as photo_count
				, count(photo_main_gallery.id) as photo_gallery_count
				, locations.name as location, locations.location_type as location_type
				, locations_parent.name as location_parent
				, translations.language, translations.medium_col1, translations.medium_col2, translations.large_col1, translations.large_col2				
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 
			LEFT JOIN photos as photo_main_gallery
				ON photo_main_gallery.id = entries.photo_id AND photo_main_gallery.deleted_flag = 0 
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			LEFT JOIN locations
				ON locations.id = entries.location_id AND locations.deleted_flag = 0
			LEFT JOIN locations as locations_parent
				ON locations_parent.id = locations.parent_id AND locations_parent.deleted_flag = 0
			LEFT JOIN translations
				ON entries.id = translations.parent_id AND translations.language = ? AND translations.parent_table = ? 
			WHERE 1=1
			AND entries.deleted_flag = 0
			';
		
		$q .= isset($permalink) ? 'AND entries.permalink = ? ' : 'AND entries.id = ? ';

		$q .= '
			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.permalink, 	entries.title, entries.description
				, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date
				, entries.photo_id, entries.parent_id, entries.site_id
				, photo, photo_title, photo_path
				, photo_gallery, photo_gallery_title, photo_gallery_path
				, location, location_parent, location_type 
				, translations.language, translations.medium_col1, translations.medium_col2, translations.large_col1, translations.large_col2 		
				LIMIT 1
		';
						
		$records = DB::select($q, [App::getLocale(), 'entries', isset($permalink) ? $permalink : $id]);
		
		if (count($records) == 1) // expected result
		{
			$record = $records[0];
			
			// translate
			if (isset($record->language))
			{
				$record->title = $record->medium_col1;
				$record->permalink = $record->medium_col2;
				$record->description = $record->large_col1;
				$record->description_short = $record->large_col2;
			}
		}
			
		return $record;
	}
	
	static public function getEntry($permalink)
	{		
		$q = '
			SELECT entries.id, entries.type_flag, entries.view_count, entries.permalink, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date, entries.photo_id, entries.parent_id 
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				, photo_main_gallery.filename as photo_gallery 
				, CONCAT(photo_main_gallery.alt_text, " - ", photo_main_gallery.location) as photo_gallery_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", photo_main_gallery.parent_id, "/") as photo_gallery_path
				, count(photos.id) as photo_count
				, count(photo_main_gallery.id) as photo_gallery_count
				, locations.name as location, locations.location_type as location_type
				, locations_parent.name as location_parent
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 
			LEFT JOIN photos as photo_main_gallery
				ON photo_main_gallery.id = entries.photo_id AND photo_main_gallery.deleted_flag = 0 
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			LEFT JOIN locations
				ON locations.id = entries.location_id AND locations.deleted_flag = 0
			LEFT JOIN locations as locations_parent
				ON locations_parent.id = locations.parent_id AND locations_parent.deleted_flag = 0
			WHERE 1=1
			AND entries.deleted_flag = 0
			AND entries.permalink = ?

			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.permalink, 	entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date, entries.photo_id, entries.parent_id
				, photo, photo_title, photo_path
				, photo_gallery, photo_gallery_title, photo_gallery_path
				, location, location_parent, location_type 
		
				LIMIT 1
		';
						
		$records = DB::select($q, [$permalink]);
		
		$records = count($records) > 0 ? $records[0] : null;
			
		return $records;
	}	
	
	static public function getEntryNEW($permalink)
	{		
		$q = '
			SELECT entries.id, entries.type_flag, entries.view_count, entries.permalink, entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date, entries.photo_id, entries.parent_id, entries.site_id
				, photo_main.filename as photo
				, CONCAT(photo_main.alt_text, " - ", photo_main.location) as photo_title
				, CONCAT("' . PHOTO_ENTRY_PATH . '", entries.id, "/") as photo_path
				
				, photo_main_gallery.filename as photo_gallery 
				, CONCAT(photo_main_gallery.alt_text, " - ", photo_main_gallery.location) as photo_title_gallery
				, CONCAT("' . PHOTO_ENTRY_PATH . '", photo_main_gallery.parent_id) as photo_path_gallery

				, count(photos.id) as photo_count
				, count(photo_main_gallery.id) as photo_gallery_count
				, locations.name as location
				, locations_parent.name as location_parent
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0 
			LEFT JOIN photos as photo_main_gallery
				ON photo_main_gallery.id = entries.photo_id AND photo_main_gallery.deleted_flag = 0 
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			LEFT JOIN locations
				ON locations.id = entries.location_id AND locations.deleted_flag = 0
			LEFT JOIN locations as locations_parent
				ON locations_parent.id = locations.parent_id AND locations_parent.deleted_flag = 0
			WHERE 1=1
			AND entries.deleted_flag = 0
			AND entries.permalink = ?

			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.permalink, 	entries.title, entries.description, entries.description_short, entries.published_flag, entries.approved_flag, entries.updated_at, entries.display_date, entries.photo_id, entries.parent_id, entries.site_id
				, photo, photo_title, photo_path
				, photo_gallery, photo_title_gallery, photo_path_gallery
				, location, location_parent
		
				LIMIT 1
		';
						
		$records = DB::select($q, [$permalink]);
		
		$records = count($records) > 0 ? $records[0] : null;
			
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
			ORDER BY entries.display_date DESC, entries.id DESC 
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


	static protected function getNextPrevEntry($display_date, $id, $next = true)
	{
		$record = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('published_flag', 1)
			->where('approved_flag', 1)
			->where('type_flag', ENTRY_TYPE_ARTICLE)
			->where('display_date', $next ? '=' : '=', $display_date)
			->where('id', $next ? '>' : '<', $id)
			->orderByRaw('display_date ' . ($next ? 'ASC' : 'DESC') . ', id ' . ($next ? 'ASC' : 'DESC '))			
			->first();

		if (!isset($record))
			$record = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('type_flag', ENTRY_TYPE_ARTICLE)
				->where('display_date', $next ? '>' : '<', $display_date)
				->orderByRaw('display_date ' . ($next ? 'ASC' : 'DESC') . ', id ' . ($next ? 'ASC' : 'DESC '))			
				->first();

		return $record;
	}
	
	static public function getAboutPage($site_id)
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
				AND entries.permalink = \'page-about\' 
			GROUP BY 
				entries.id, entries.title, entries.description, entries.permalink, photo, photo_title, photo_path, entries.published_flag, entries.approved_flag 
			LIMIT 1
		';
		
		$record = DB::select($q, [$site_id, $site_id]);
		
		return $record;
	}
	
	static protected function getEntryCount($entry_type, $allSites)
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
	
	static public function getStats()
	{
		$stats = [];
		
		$stats['articles'] = Entry::getEntryCount(ENTRY_TYPE_ARTICLE, /* allSites = */ false);
		$stats['blogs'] = Entry::getEntryCount(ENTRY_TYPE_BLOG, /* allSites = */ false);
		$stats['blog-entries'] = Entry::getEntryCount(ENTRY_TYPE_BLOG_ENTRY, /* allSites = */ false);
		$stats['tours'] = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* allSites = */ false);
		
		return $stats;
	}
}
