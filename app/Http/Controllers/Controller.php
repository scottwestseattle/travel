<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime;

define('SITE_ID', intval(env('SITE_ID')));
define('PHOTO_SERVER', env('PHOTO_SERVER'));

use DB;
use Auth;
use App\Entry;
use App\Event;
use App\Location;
use App\Photo;
use App\Site;
use App\Task;
use App\Visitor;
use App\Category;
use App\Account;

define('ERROR_REDIRECT_PAGE', '/error');

define('BODY_PLACEHODER', '[[body]]'); // tag that gets replaced with the body of the template

// photos
define('TOUR_PHOTOS_PATH', '/public/img/tours/');
define('SLIDER_PHOTOS_PATH', '/public/img/sliders/');
define('PHOTOS_FULL_PATH', '/public/img/');
define('PHOTOS_WEB_PATH', '/img/');
define('PHOTOS_THUMBNAIL_FOLDER', 'tn');

// -1=not set, 0=slider, 1=entry, 2=receipt, 99=other 
define('PHOTO_TYPE_NOTSET',		-1);
define('PHOTO_TYPE_SLIDER',		0);
define('PHOTO_TYPE_ENTRY', 		1);
define('PHOTO_TYPE_RECEIPT', 	2);
define('PHOTO_TYPE_OTHER', 		99);

define('TOUR_PHOTO_PLACEHOLDER', 'img/theme1/entry-placeholder.jpg');
define('PHOTO_THUMBNAIL_HEIGHT', 400);

define('PHOTO_SLIDER_FOLDER', 'sliders');
define('PHOTO_ENTRY_FOLDER', 'entries');
define('PHOTO_RECEIPT_FOLDER', 'receipts');
define('PHOTO_TMP_FOLDER', 'tmp');
define('PHOTO_ENTRY_PATH', '/img/entries/');
define('PHOTO_SLIDER_PATH', '/img/sliders/');

// users
define('EXT_JPG', '.jpg');
define('USER_UNCONFIRMED', 0);		// user unconfirmed
define('USER_CONFIRMED', 10);		// user confirmed
define('USER_WRITER', 20);			// article/tour write
define('USER_EDITOR', 30);			// content editor
define('USER_SITE_ADMIN', 100);		// user site admin
define('USER_SUPER_ADMIN', 1000);	// user super admin

// locations
define('LOCATION_TYPE_PLANET', 0);
define('LOCATION_TYPE_CONTINENT', 100);
define('LOCATION_TYPE_SUBCONTINENT', 200);
define('LOCATION_TYPE_COUNTRY', 300);
define('LOCATION_TYPE_REGION', 400);
define('LOCATION_TYPE_STATE', 500);
define('LOCATION_TYPE_ZONE', 600);
define('LOCATION_TYPE_CITY', 700); // City or Place, such as Seattle or Olympic National Park
define('LOCATION_TYPE_NEIGHBORHOOD_AREA', 800); // Neighborhood OR Area, such as West Seattle or Downtown
define('SHOW_NON_XS', 'hidden-xs');
define('SHOW_XS_ONLY', 'hidden-xl hidden-lg hidden-md hidden-sm');
define('VISITOR_MAX_LENGTH', 200);

// entries
// -1=not set, 1=entry, 2=tour/hike, 3=blog, 4=blog entry, 5=article, 6=note, 7=other 	
define('ENTRY_TYPE_NOTSET', 	-1);
define('ENTRY_TYPE_ENTRY', 		1);
define('ENTRY_TYPE_TOUR', 		2);
define('ENTRY_TYPE_BLOG', 		3);
define('ENTRY_TYPE_BLOG_ENTRY', 4);
define('ENTRY_TYPE_ARTICLE', 	5);
define('ENTRY_TYPE_NOTE', 		6);
define('ENTRY_TYPE_SECTION',	7);
define('ENTRY_TYPE_GALLERY',	8);
define('ENTRY_TYPE_OTHER',		99);

// sections
define('SECTION_AFFILIATES', 'section-affiliates');
define('SECTION_ARTICLES', 'section-articles');
define('SECTION_BLOGS', 'section-blogs');
define('SECTION_CURRENT_LOCATION', 'section-current-location');
define('SECTION_GALLERY', 'section-gallery');
define('SECTION_SLIDERS', 'section-sliders');
define('SECTION_TOURS', 'section-tours');
define('SECTION_WELCOME', 'section-welcome');
define('SECTION_CASH', 'section-cash');

// event logger info
define('LOG_TYPE_INFO', 1);
define('LOG_TYPE_WARNING', 2);
define('LOG_TYPE_ERROR', 3);
define('LOG_TYPE_EXCEPTION', 4);
define('LOG_TYPE_OTHER', 99);
	
define('LOG_MODEL_ARTICLES', 'articles');
define('LOG_MODEL_BLOGS', 'blogs');
define('LOG_MODEL_BLOG_ENTRIES', 'blog entries');
define('LOG_MODEL_ENTRIES', 'entries');
define('LOG_MODEL_LOCATIONS', 'locations');
define('LOG_MODEL_OTHER', 'other');
define('LOG_MODEL_PHOTOS', 'photos');
define('LOG_MODEL_SECTIONS', 'sections');
define('LOG_MODEL_SITES', 'sites');
define('LOG_MODEL_TOURS', 'tours');
define('LOG_MODEL_USERS', 'users');
define('LOG_MODEL_TEMPLATES', 'templates');
	
define('LOG_ACTION_ACCESS', 'access');
define('LOG_ACTION_ADD', 'add');
define('LOG_ACTION_EDIT', 'edit');
define('LOG_ACTION_DELETE', 'delete');
define('LOG_ACTION_VIEW', 'view');
define('LOG_ACTION_SELECT', 'select');
define('LOG_ACTION_MOVE', 'move');
define('LOG_ACTION_UPLOAD', 'upload');
define('LOG_ACTION_MKDIR', 'mkdir');
define('LOG_ACTION_RESIZE', 'resize');
define('LOG_ACTION_OTHER', 'other');

define('LOG_PAGE_INDEX', 'index');
define('LOG_PAGE_VIEW', 'view');
define('LOG_PAGE_SHOW', 'show');
define('LOG_PAGE_GALLERY', 'gallery');
define('LOG_PAGE_SLIDERS', 'sliders');
define('LOG_PAGE_PERMALINK', 'permalink');
define('LOG_PAGE_MAPS', 'maps');
define('LOG_PAGE_LOCATION', 'location');
define('LOG_PAGE_ABOUT', 'about');
define('LOG_PAGE_CONFIRM', 'confirm login');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $prefix = 'prefix';
	protected $title = 'Title';
	private $viewData = [];
	
	static private $entryTypes = [
		ENTRY_TYPE_NOTSET => 'Not Set',
		ENTRY_TYPE_ARTICLE => 'Article',
		ENTRY_TYPE_BLOG => 'Blog',
		ENTRY_TYPE_BLOG_ENTRY => 'Blog Entry',
		ENTRY_TYPE_ENTRY => 'Entry',
		ENTRY_TYPE_GALLERY => 'Gallery',
		ENTRY_TYPE_NOTE => 'Note',
		ENTRY_TYPE_OTHER => 'Other',
		ENTRY_TYPE_SECTION => 'Section',
		ENTRY_TYPE_TOUR => 'Tour/Hike',
	];
		
	public function __construct ()
	{
	}
	
	static public function getEntryTypes()
	{		
		return Controller::$entryTypes;
	}
		
	protected function getVisitorInfo(&$host, &$referrer, &$userAgent)
	{
		//
		// get visitor info
		//
		$ip = Event::getVisitorIp();
						
		$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);		

		$referrer = null;
		if (array_key_exists("HTTP_REFERER", $_SERVER))
			$referrer = $_SERVER["HTTP_REFERER"];

		$userAgent = null;
		if (array_key_exists("HTTP_USER_AGENT", $_SERVER))
			$userAgent = $_SERVER["HTTP_USER_AGENT"];

		return $ip;
	}
	
	protected function saveVisitor($model, $page, $record_id = null)
	{		
		$spy = session('spy', null);
		if (isset($spy))
			return; // spy mode, don't count views
	
		if ($this->isAdmin())
			return; // admin user, don't count views

		$save = false;
		$host = null;
		$referrer = null;
		$userAgent = null;
		
		$ip = $this->getVisitorInfo($host, $referrer, $userAgent);
		
		if (strlen($userAgent) == 0 && strlen($referrer) == 0)
		{
			return; // no host or referrer probably means that it's the ETG tester so don't count it
		}
		
		$visitor = null;
		if (false)
		{
			// the original way without page info
			$visitor = Visitor::select()
				->where('ip_address', '=', $ip)
				->where('deleted_flag', 0)
				->first();
		}
			
		if (!isset($visitor)) // new visitor
		{
			$visitor = new Visitor();
			$visitor->ip_address = $ip;	
		}
		
		$visitor->visit_count++;			
		$visitor->site_id = SITE_ID;
		$visitor->host_name = $this->trunc($host, VISITOR_MAX_LENGTH);
		$visitor->user_agent = $this->trunc($userAgent, VISITOR_MAX_LENGTH);
		//$visitor->user_agent = "host=" . $host . ', refer=' . $referrer . ', user=' . $userAgent;
		$visitor->referrer = $this->trunc($referrer, VISITOR_MAX_LENGTH);
		
		// new fields
		$visitor->model = $model;
		$visitor->page = $page;
		$visitor->record_id = $record_id;
		
		$visitor->save();		
	}	

	protected function trunc($string, $length)
	{
		$ellipsis = '...';
		$newLength = $length - strlen($ellipsis);
		$string = (strlen($string) > $length) ? substr($string, 0, $newLength) . $ellipsis : $string;
		
		return $string;
	}	
	
	protected function isNewVisitor()
	{
		$ip = Event::getVisitorIp();
		
		$visitor = Visitor::select()
			->where('ip_address', '=', $ip)
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->first();
		
		return(!isset($visitor));
	}		
	
	protected function isOwnerOrAdmin($user_id)
	{
		$rc = false;
		
		// if owner
		if ($this->isOwner($user_id))
		{
			$rc = true;
		}

		// if site or super admin
		if ($this->isAdmin())
		{
			$rc = true;
		}

		return $rc;
	}

	protected function isOwner($user_id)
	{
		return (Auth::check() && Auth::id() == $user_id);
	}	

	protected function isAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SITE_ADMIN);
	}	
	
	protected function isSuperAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SUPER_ADMIN);
	}	
	
	protected function getViewData($vdata = null)
	{			
		$this->viewData = isset($vdata) ? $vdata : [];
		
		// add-on the mandatory parts
		$this->viewData['sections'] = Controller::getSections();
		$this->viewData['site'] = Controller::getSite();
		$this->viewData['prefix'] = $this->prefix;
		$this->viewData['title'] = $this->title;
		$this->viewData['titlePlural'] = ucwords($this->prefix);
		
		return $this->viewData;
	}
	
	protected function formatLinks($text)
	{
		$lines = explode("\r\n", $text);

		$text = '';
		
		foreach($lines as $line)
		{
				preg_match('/\[(.*?)\]/', $line, $title);	// replace the chars between []
				preg_match('/\((.*?)\)/', $line, $link);	// replace the chars between ()
				
				if (sizeof($title) > 0 && sizeof($link)) // if its a link
				{
					$text .= '<div style="font-family: \'Raleway\';font-weight:bold;"><a style="font-size:.9em; color:#4993FD;" href=' . $link[1] . ' target="_blank">' . $title[1] . '</a></div>';
				}
				else if (mb_strlen($line) === 0) // blank line
				{
					$text .= $line;
				}
				else // regular line with text
				{
					$text .= $line;
				}
		}
		
		return $text;
	}

    protected function getSliders()
    {
		$path = base_path() . SLIDER_PHOTOS_PATH;
		$files = scandir($path);
		$photos = [];
		foreach($files as $file)
		{
			if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
			{
				if (/* $this->startsWith($file, 'slider') && */ Controller::endsWith($file, '.jpg'))
				{
					$photos[] = $file;					
				}
			}
		}
				
		return $photos;
    }	
	
    protected function getPhotosWebPath($subfolder = '')
    {
		$path = PHOTOS_WEB_PATH;
		
		if (strlen($subfolder) > 0)
			$path .= $subfolder;
			
		if (!Controller::endsWith($path, '/'))
			$path .= '/';
					
		return $path;
	}

    static protected function getPhotoPathRemote($subfolder, $site_id)
    {		
		if ($site_id == SITE_ID)
		{
			// nothing to do since the photo is local to the original site
			$path = $subfolder;
		}
		else
		{
			$path = PHOTO_SERVER;
			//$path = 'http://localhost/';
			
			if (strlen($subfolder) > 0)
				$path .= $subfolder;
				
			if (!Controller::endsWith($path, '/'))
				$path .= '/';
		}
					
		return $path;
	}
	
    protected function getPhotosFullPath($subfolder = '')
    {
		$path = base_path() . PHOTOS_FULL_PATH;
		
		if (strlen($subfolder) > 0)
			$path .= $subfolder;
			
		if (!Controller::endsWith($path, '/'))
			$path .= '/';
		
		return $path;
	}
	
    protected function getPhotos($subfolder = '', $ext = EXT_JPG)
    {
		$photos = [];
		$path = $this->getPhotosFullPath($subfolder, $ext);
		
		if (is_dir($path))
		{
			$files = scandir($path);		
			
			foreach($files as $file)
			{
				if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
				{
					if (Controller::endsWith($file, $ext))
					{
						$photos[] = $file;					
					}
				}
			}
		}
				
		return $photos;
    }	
	
    protected function getPhotosYola($id, $ext)
    {
		$path = base_path() . TOUR_PHOTOS_PATH . $entry->id;
		
		//Debugger::dump('path: ' . $path);
			
		$files = scandir($path);						
		foreach($files as $file)
		{
			if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
			{
				$photos[] = $file;					
			}
		}
					
		/*
			$thumbs_path = $this->getGalleryPath($gallery . '/thumbs' . $width, $user_id);
			$files = scandir($thumbs_path);	
			$photos_thumbs = array();
			foreach($files as $file)
			{
				if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
				{
					$photos_thumbs[] = $file;					
				}
			}
			
			//Debugger::dump('thumbs_path: ' . $thumbs_path);
	
			// if big photos and thumb lists don't match, create the thumbs
			if ($photos != $photos_thumbs)
			{	
				echo  'processing ' . (count($photos) - count($photos_thumbs)) . ' photos...';
			
				//Debugger::dump($photos);
				//Debugger::dump($photos_thumbs);
				//die;
				
				//
				// if thumbs are missing create them first
				//
				foreach($photos as $file)
				{
					$file_thumb = $thumbs_path . '/' . $file;
					//Debugger::dump($file_thumb);//die($file_thumb);						
					//Debugger::dump('file: '. $file);
					
					// create the thumb if it's not already there and the right size
					$this->makeThumb($path, $thumbs_path, $file, $width, true);
				}
				
				//
				// check for orphan thumbs (big photo no longer exists so delete thumb)
				//
				foreach($photos_thumbs as $file)
				{
					$file_main = $path . '/' . $file;
					
					if (!file_exists($file_main))
					{
						//Debugger::dump('no main for thumb: ' . $file_main);
						
						$file_thumb = $thumbs_path . '/' . $file;
						//Debugger::dump('deleting: ' . $file_thumb);
						$this->deleteFile($file_thumb);
					}
				}				
			}
			else if ($fixThumbs != '')
			{
				//
				// all thumbs are there, check for right size
				//
				foreach($photos as $file)
				{
					$file_thumb = $thumbs_path . '/' . $file;
					//Debugger::dump($file_thumb);//die($file_thumb);
											
					$this->makeThumb($path, $thumbs_path, $file, $width, false);
				}
			}	
		*/
		
		return $photos;
    }	
	
	protected function startsWith($haystack, $needle)
	{
		$rc = false;
		$pos = strpos($haystack, $needle);

		if ($pos === false) 
		{
			// not found
		} 
		else 
		{
			// found, check for pos == 0
			if ($pos === 0)
			{
				$rc = true;
			}
			else
			{
				// found but string doesn't start with it
			}
		}
		
		return $rc;
	}
	
	static protected function endsWith($haystack, $needle)
	{
		$rc = false;
		$pos = strrpos($haystack, $needle);

		if ($pos === false) 
		{
			// not found
		} 
		else 
		{
			// found, check for pos == 0
			if ($pos === (strlen($haystack) - strlen($needle)))
			{
				$rc = true;
			}
			else
			{
				// found but string doesn't start with it
			}
		}
		
		return $rc;
	}

    static protected function getPhotoPath(Photo $photo = null)
    {
		if ($photo == null)
		{
			// the higher level photo path
			$path = '/img/entries/';
		}
		else
		{
			$info = Controller::getPhotoInfoPath($photo->type_flag, $photo->parent_id);
			$path = $info['path'];
		}
		
		return $path;
	}

	static protected function getPhotoInfoPath($type_flag, $parent_id)
	{		
		$info = Controller::getPhotoInfo($type_flag);
		
		$info['path'] = '/img/' . $info['folder'];
		$info['redirect'] = '/photos/' . $info['redirect'];

		if (!Controller::isSlider($type_flag))
		{
			$info['path'] .= '/' . $parent_id;
			$info['redirect'] .= '/' . intval($parent_id) . '/' . intval($type_flag);
		}

		$info['filepath'] = base_path() . '/public' . $info['path'];

		return $info;
	}
	
	static protected function getPhotoInfo($type_flag)
	{		
		// default to these for backwards compatibility
		$folder = PHOTO_ENTRY_FOLDER;
		$type = 'Entry';
		$redirect = $folder; // physical folder and url folder are the same
		
		switch($type_flag)
		{
			case PHOTO_TYPE_ENTRY:
				// already set above for default
				break;
			case PHOTO_TYPE_RECEIPT:
				$folder = PHOTO_RECEIPT_FOLDER; // physical folder is different, 
				$redirect = PHOTO_ENTRY_FOLDER; // url folder for redirect NOT different
				$type = 'Receipt';
				break;
			case PHOTO_TYPE_SLIDER:
				$folder = PHOTO_SLIDER_FOLDER;
				$redirect = 'sliders';
				$type = 'Slider';
				break;
			default:
				break;
		}

		$info = [
			'type' => $type,
			'folder' => $folder,
			'redirect' => $redirect,
		];

		return $info;
	}
	
	static protected function deletePhoto(Photo $photo, &$redirect, &$message, &$messageLevel)
	{
		$redirect = '/';
		$message = 'Photo successfully deleted';
		$messageLevel = 'success';
		$rc = false;
			
		// 
		// update the database record
		//
		$photo->deleteSafe();
		//$photo->deleted_flag = 1;
		//$photo->save();	

		//
		// move the file to the deleted folder
		//
		$info = Controller::getPhotoInfoPath($photo->type_flag, $photo->parent_id);
		$folder = $info['folder'];
		$redirect = $info['redirect'];
		$path_from = $info['filepath'];
		
		$path_to = $path_from . '/deleted';
					
		if (!is_dir($path_to)) 
		{
			if (is_dir($path_from)) 
			{
				// make the folder with read/execute for everybody
				mkdir($path_to, 0755);
			}			
		}
		
		$path_from .= '/' . $photo->filename;
		$path_to .= '/' . $photo->filename;

		try
		{
			rename($path_from, $path_to);
			
			$rc = true;
		}
		catch (\Exception $e) 
		{
			$messageLevel = 'danger';
			$message = $e->getMessage();				
		}
		
		return $rc;
	}
	
	protected function getTourIndexAdmin($pending = false)
	{
		$q = '
			SELECT entries.id, entries.title, entries.location_id, entries.view_count, entries.published_flag, entries.approved_flag, entries.permalink,
				activities.id as activity_id,
				activities.map_link,
				photo_main.filename as photo,
				count(photos.id) as photo_count
			FROM entries
			LEFT JOIN activities
				ON activities.parent_id = entries.id
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			WHERE 1=1
				AND entries.site_id = ? 
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
		';
		
		if ($pending)
		{
			$q .= ' AND (entries.approved_flag = 0 OR entries.published_flag = 0 OR entries.location_id IS NULL	OR entries.location_id = 0) ';
		}	

		$q .= '
			GROUP BY 
				entries.id, entries.title, entries.location_id, entries.view_count, entries.published_flag, entries.approved_flag, entries.permalink,
				activities.id, photo_main.filename, activities.map_link, activities.location_id
			ORDER BY entries.published_flag ASC, entries.approved_flag ASC, activities.map_link ASC, entries.updated_at DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [SITE_ID, ENTRY_TYPE_TOUR]);
		
		return $records;
	}
		
	protected function getTourIndex($allSites = false)
	{
		$q = '
			SELECT entries.id, entries.title, entries.permalink, entries.site_id, 
				photo_main.filename as photo
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0
			WHERE 1=1 ';
			
		if (!$allSites)
			$q .= ' AND entries.site_id = ' . SITE_ID . ' ';
		
		$q .= '
				AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
			GROUP BY 
				entries.id, entries.title, photo_main.filename, entries.permalink, entries.site_id
			ORDER BY entries.id DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [ENTRY_TYPE_TOUR]);
		
		return $records;
	}	

	protected function getTourIndexLocation($location_id, $allSites)
	{
		$q = '
			SELECT entries.id, entries.title, entries.permalink, photo_main.filename as photo
			FROM entries
			LEFT JOIN photos as photo_main
				ON photo_main.parent_id = entries.id AND photo_main.main_flag = 1 AND photo_main.deleted_flag = 0			
			JOIN entry_location entloc ON entries.id = entloc.entry_id
			JOIN locations ON entloc.location_id = locations.id AND locations.id = ?
			WHERE 1=1 ';
			
		if (!$allSites)
			$q .= ' AND entries.site_id = ' . SITE_ID . ' ';
		
		$q .= '	AND entries.type_flag = ?
				AND entries.deleted_flag = 0
				AND entries.published_flag = 1 
				AND entries.approved_flag = 1
			GROUP BY 
				entries.id, entries.title, entries.permalink, photo_main.filename
			ORDER BY entries.id DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [$location_id, ENTRY_TYPE_TOUR]);
		
		return $records;
	}	
	
	static protected function isSlider($type_flag)
	{
		return intval($type_flag) === PHOTO_TYPE_SLIDER;
	}
	
    protected function saveLocations($entry, $locations)
    {	
		if (isset($entry) && isset($locations))
		{
			$this->saveLocation($entry, $locations->loc1_id);
			$this->saveLocation($entry, $locations->loc2_id);
			$this->saveLocation($entry, $locations->loc3_id);
			$this->saveLocation($entry, $locations->loc4_id);
			$this->saveLocation($entry, $locations->loc5_id);
			$this->saveLocation($entry, $locations->loc6_id);
			$this->saveLocation($entry, $locations->loc7_id);
			$this->saveLocation($entry, $locations->loc8_id);
		}
	}

    protected function saveLocation($entry, $id)
    {	
		$rc = false;
		
		if (isset($id))
		{
			$record = Location::select()
					->where('id', '=', $id)
					->first();
					
			if (isset($record))
			{
				$entry->locations()->save($record);
				$rc = true;
			}
			else
			{
				//todo: log this 'location record not found'
			}
		}
		else
		{
			// valid condition because the records don't have all location levels
			$rc = true;
		}

		return $rc;
	}

	protected function getReferer($request, $default)
	{
		$referer = isset($request) && isset($request->referer) ? $request->referer : $default;
		
		return $referer;
	}
	
    protected function copyDirty($to, $from, &$isDirty, &$updates = null)
    {	
		$from = $this->trimNull($from);
		$to = $this->trimNull($to);
		
		if ($from != $to)
		{
			$isDirty = true;
			
			if (!isset($updates) || strlen($updates) == 0)
				$updates = '';

			$updates .= '|';
				
			if (strlen($to) == 0)
				$updates .= '(empty)';
			else
				$updates .= $to;

			$updates .= '|';
				
			if (strlen($from) == 0)
				$updates .= '(empty)';
			else
				$updates .= $from;
			
			$updates .= '|  ';
		}
		
		return $from;
	}	
	
	// if string has non-whitespace chars, then it gets trimmed, otherwise gets set to null
	protected function trimNull($text)
	{
		if (isset($text))
		{
			$text = trim($text);
			
			if (strlen($text) === 0)
				$text = null;
		}
		
		return $text;
	}
	
	static protected function trimNullStatic($text)
	{
		if (isset($text))
		{
			$text = trim($text);
			
			if (strlen($text) === 0)
				$text = null;
		}
		
		return $text;
	}
	
	protected function getTextOrShowEmpty($text)
	{
		$r = '(empty)';
		
		if (isset($text))
		{
			$text = trim($text);
			
			if (strlen($text) === 0)
				$text = null;
			else
				$r = $text;
		}
		
		return $r;
	}
	
    public function createPermalink($title, $date = null)
    {		
		$ret = null;
		
		if (isset($title))
		{
			$ret = $title;
		}
		
		if (isset($date))
		{
			$ret .= '-' . $date;
		}
		
		$ret = preg_replace('/[^\da-z ]/i', ' ', $ret); // remove all non-alphanums
		$ret = str_replace(" ", "-", $ret);				// replace spaces with dashes
		$ret = strtolower($ret);						// make all lc
		$ret = $this->trimNull($ret);					// trim it or null it
		
		return $ret;
	}
	
    protected function getSite()
    {		
		$site = null;
		
		try 
		{
			$site = Site::select()
				->where('id', SITE_ID)
				->where('deleted_flag', 0)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error loading Front Page Sites';
			Event::logException(LOG_MODEL_SITES, LOG_ACTION_SELECT, $msg, null, $e->getMessage());
		}
		
		if (isset($site))
		{
			// massage data?
		}
		else
		{
			$msg = 'Front Page: Site Not Found, Site ID: ' . SITE_ID;
			Event::logError(LOG_MODEL_SITES, LOG_ACTION_SELECT, $msg);
		}
		
		return $site;
	}		

    static protected function fixLinks($link)
    {		
		$link = str_replace('[iframe', '<iframe', $link);
		$link = str_replace('[/iframe', '</iframe', $link);
		
		return $link;
	}
	
    protected function getSections()
    {		
		$sections = null;
		
		try 
		{
			$sections = Entry::getEntriesByType(ENTRY_TYPE_SECTION);
		}
		catch (\Exception $e)
		{
			$msg = 'Error loading Front Page Sections';
			
			Event::logException(LOG_MODEL_SECTIONS, LOG_ACTION_SELECT, $msg, null, $e->getMessage());
		}
		
		// put the sections in a named array for key access
		if (isset($sections))
		{
			$sectionArray = [];
			foreach($sections as $section)
			{
				$sectionArray[$section->permalink] = $section;
			}
			
			$sections = $sectionArray;
		}
		else
		{
			$msg = 'Front Page: No Sections Found';
			Event::logError(LOG_MODEL_SECTIONS, LOG_ACTION_SELECT, $msg);
		}			
		
		return $sections;
	}
	
    public function getCategories($action)
    {
		$error = '';
		$records = Category::getArray($error);
		
		if (count($records) == 0)
			Event::logError(LOG_MODEL, $action, 'Error Getting Category List', null, null, $error);
		
		return $records;
	}

    public function getSubcategories($action, $category_id = null)
    {
		$error = '';
		
		$records = Category::getSubcategoryOptions($category_id);
		
		if (count($records) == 0)
			Event::logError(LOG_MODEL, $action, 'Error Getting Subcategory List', null, null, $error);
		
		return $records;
	}
	
    public function getAccounts($action)
    {
		$error = '';
		$records = Account::getArray($error);
		
		if (count($records) == 0)
			Event::logError(LOG_MODEL, $action, 'Error Creating Account List', null, null, $error);

		return $records;
	}
		
    static protected function getDateControlDates()
    {
		$months = [
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December',
		];		

		$days = [];
		for ($i = 1; $i <= 31; $i++)
			$days[$i] = $i;

		$years = [];		
		for ($i = 2010; $i <= 2050; $i++)
		{
			if ($i > intval(date('Y'))) // only go up to the current year
				break;
				
			$years[$i] = $i;	
		}			
			
		$dates = [
			'months' => $months,
			'years' => $years,
			'days' => $days,
		];

		return $dates;
	}

    static protected function getSelectedDate($request)
    {
		$filter = Controller::getFilter($request);
		
		$date = Controller::trimNullStatic($filter['from_date']);
		
		return $date;
	}				
	
    static protected function getFilter($request, $today = false, $month = false)
    {
		$filter = Controller::getDateFilter($request, $today, $month);
	
		$filter['account_id'] = false;
		$filter['category_id'] = false;
		$filter['subcategory_id'] = false;
		$filter['search'] = false;
		$filter['unreconciled_flag'] = false;
		$filter['unmerged_flag'] = false;
		
		if (isset($request->account_id))
		{
			$id = intval($request->account_id);
			if ($id > 0)
				$filter['account_id'] = $id;
		}
		
		if (isset($request->category_id))
		{
			$id = intval($request->category_id);
			if ($id > 0)
				$filter['category_id'] = $id;
		}
		
		if (isset($request->subcategory_id))
		{
			$id = intval($request->subcategory_id);
			if ($id > 0)
				$filter['subcategory_id'] = $id;
		}

		if (isset($request->search))
		{
			if (strlen($request->search) > 0)
				$filter['search'] = $request->search;
		}

		if (isset($request->unreconciled_flag))
		{
			$filter['unreconciled_flag'] = $request->unreconciled_flag;
		}

		if (isset($request->unmerged_flag))
		{
			$filter['unmerged_flag'] = $request->unmerged_flag;
		}
		
		return $filter;
	}
	
    static protected function getDateFilter($request = false, $today, $monthFlag)
    {
		$dates = [];
		
		$dates['selected_month'] = false;
		$dates['selected_day'] = false;
		$dates['selected_year'] = false;
		
		$month = 0;
		$year = 0;
		$day = 0;
		
		if (isset($request) && (isset($request->day) && $request->day > 0 || isset($request->month) && $request->month > 0 || isset($request->year) && $request->year > 0))
		{
			// date filter is on, use it
			if (isset($request->month))
				if (($month = intval($request->month)) > 0)
					$dates['selected_month'] = $month;
			
			if (isset($request->day))
				if (($day = intval($request->day)) > 0)
					$dates['selected_day'] = $day;
			
			if (isset($request->year))
				if (($year = intval($request->year)) > 0)
					$dates['selected_year'] = $year;
		}
		else
		{
			if ($today)
			{
				$month = intval(date("m"));
				$year = intval(date("Y"));

				// if we're showing a month then we put then day will be false
				$day = $monthFlag ? false : intval(date("d"));

				// if nothing is set use current month
				$dates['selected_day'] = $day;
				$dates['selected_month'] = $month;
				$dates['selected_year'] = $year;
			}
			else
			{
				$dates['from_date'] = null;
				$dates['to_date'] = null;
				
				return $dates;
			}
		}
		
		//
		// put together the search dates
		//
		
		// set month range
		$fromMonth = 1;
		$toMonth = 12;
		if ($month > 0)
		{
			$fromMonth = $month;
			$toMonth = $month;
		}	

		// set year range
		$fromYear = 2010;
		$toYear = 2050;
		if ($year > 0)
		{
			$fromYear = $year;
			$toYear = $year;
		}
		else
		{
			// if month set without the year, default to current year
			if ($month > 0)
			{
				$fromYear = intval(date("Y"));
				$toYear = $fromYear;
			}
		}
	
		$fromDay = 1;
		$toDate = "$toYear-$toMonth-01";
		$toDay = intval(date('t', strtotime($toDate)));
		
		if ($day > 0)
		{
			$fromDay = $day;
			$toDay = $day;
		}
		
		$dates['from_date'] = '' . $fromYear . '-' . $fromMonth . '-' . $fromDay;
		$dates['to_date'] = '' . $toYear . '-' . $toMonth . '-' . $toDay;
				
		return $dates;
	}
	
    static protected function getDateControlSelectedDate($date)
    {
		$date = DateTime::createFromFormat('Y-m-d', $date);
		
		$parts = [
			'selected_day' => intval($date->format('d')),
			'selected_month' => intval($date->format('m')),
			'selected_year' => intval($date->format('Y')),
		];
		
		return $parts;
	}
	
    static protected function getUniqueFilename($path, $filename)
    {
		$filenameOrig = $filename;
		
		for($i = 0; $i < 100; $i++)
		{
			$fullPath = Controller::appendPath($path, $filename);

			if (file_exists($fullPath))
			{
				$base = basename($filenameOrig, ".jpg");
				$base = basename($base, ".JPG");
				
				$filename =  $base . '(' . ($i+1) . ').jpg';
			}
			else
			{
				break;
			}
		}	
		
		return $filename;
	}

    static protected function appendPath($path, $filename)
    {
		if (!Controller::endsWith($path, '/'))
			$path .= '/';
			
		return $path . $filename;
	}
	
	static public function getEntriesByType($type_flag, $approved_flag = true, $limit = 0, $all_sites = false)
	{
		$records = Entry::getEntriesByType($type_flag, $approved_flag, $limit, $all_sites);
		
		// fix-up the photo paths
		foreach($records as $record)
		{
			//dd($record);
			
			if (isset($record->photo_gallery))
			{
				$record->photo = $record->photo_gallery;
				$record->photo_path = Controller::getPhotoPathRemote($record->photo_path_gallery, $record->site_id);
				
				Controller::makeThumbnail($record);
			}
			else if (isset($record->photo))
			{
				// photo name already set correctly
				$record->photo_path = Controller::getPhotoPathRemote($record->photo_path, $record->site_id);
				
				Controller::makeThumbnail($record);
			}
			else
			{
				$record->photo = TOUR_PHOTO_PLACEHOLDER;
				$record->photo_path = '';
			}

			//echo 'folder: ' . $record->photo_path . '/' . $record->photo . '<br/>';
		}
		
		//die;
		
		return $records;
	}
	
	static public function makeThumbnail($record)
	{
		if ($record->site_id != SITE_ID)
		{
			$record->photo_path = $record->photo_path . '/tn';
			return;
		}
			
		$tnFolder = base_path() . '/public' . $record->photo_path . '/' . PHOTOS_THUMBNAIL_FOLDER; 
		$found = false;
		
		if (is_dir($tnFolder))
		{
			// does TN already exists for this photo
			$tn = $tnFolder . '/' . $record->photo;
			
			if (file_exists($tn))
			{
				// TN already exists
				$found = true;
			}
		}
		else
		{
			// create the TN folder and the TN for this photo
			try
			{
				mkdir($tnFolder, 0755);
			}
			catch (\Exception $e) 
			{	
				// log exception
				Event::logException(LOG_MODEL_PHOTOS, LOG_ACTION_MKDIR, 'Error creating TN folder: ' . $tnFolder, null, $e->getMessage());
			}			
		}
		
		if (!$found)
		{
			$fromPath = base_path() . '/public' . $record->photo_path;
			
			try
			{
				if (Controller::resizeImage($fromPath, $tnFolder, $record->photo, $record->photo, /* new height = */ PHOTO_THUMBNAIL_HEIGHT))
				{
					// log results
					$msg = 'TN created for photo ' . $record->photo . ' in folder ' . $tnFolder;
					Event::logInfo(LOG_MODEL, LOG_ACTION_RESIZE, $msg);
				}
				else
				{
					// log error
					$msg = 'Error resizing image: photo=' . $record->photo . ', folder=' . $tnFolder;
					Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_RESIZE, $msg);
				}
			}
			catch (\Exception $e) 
			{	
				// log exception
				$msg = 'Error creating TN for photo ' . $record->photo . ' in folder ' . $tnFolder;
				Event::logException(LOG_MODEL_PHOTOS, LOG_ACTION_RESIZE, $msg, null, $e->getMessage());
			}				
		}
		
		$record->photo_path = $record->photo_path . '/tn';
	}
	
	static protected function resizeImage($fromPath, $toPath, $filename, $filenameTo, $heightNew)
	{	
		//dd($toPath);
		
		if (!is_dir($toPath)) 
		{
			mkdir($toPath, 0755);// make the folder with read/execute for everybody
		}
		
		//
		// get image info
		//
		//Debugger::dump('from: ' . $toPath);die;	
			
		$file = $fromPath;
						
		$file = Controller::appendPath($file, $filename);
		$fileThumb = Controller::appendPath($toPath, $filenameTo);
				
		$image_info = getimagesize($file);
		
		switch($image_info["mime"])
		{
			case "image/jpeg":
				$image = @imagecreatefromjpeg($file); //jpeg file
				break;
				
			case "image/gif":
				$image = @imagecreatefromgif($file); //gif file
				break;
				
			case "image/png":
				$image = @imagecreatefrompng($file); //png file
				break;
				
			default: 
				$image = false;
				break;
		}
		
		// check for bad image
		if (!$image)
		{
			//Debugger::dump('filename = ' . $filename);
			//Debugger::dump('file = ' . $file);
			//Debugger::dump($image_info);
	
			return false;
		}
		
		//
		// resize the file
		//
		
		$portrait = (imagesy($image) > imagesx($image));
		
		$width = 0;
		$height = $heightNew;
		
		if ($portrait)
		{
			$ratio = $height / imagesy($image);
			$width = imagesx($image) * $ratio; 			
		}
		else
		{
			$ratio = $height / imagesy($image);			
			$width = imagesx($image) * $ratio; 			
		}

		//sbw $fileThumb = $toPath . '/' . $filename;

		if (false /*sbw*/ && file_exists($fileThumb))
		{			
			// check the thumb
			$image_info_thumb = getimagesize($fileThumb);
			
			switch($image_info_thumb["mime"])
			{
				case "image/jpeg":
					$imageThumb = @imagecreatefromjpeg($fileThumb); //jpeg file
					break;
					
				case "image/gif":
					$imageThumb = @imagecreatefromgif($fileThumb); //gif file
					break;
					
				case "image/png":
					$imageThumb = @imagecreatefrompng($fileThumb); //png file
					break;
					
				default: 
					$imageThumb = false;
					break;
			}
			
			// check for bad image
			if (!$imageThumb)
			{		
				return false;
			}	

			//Debugger::dump( 'r = ' . $ratio .', h = ' . $height . ', w = ' . $width . ', ' . $portrait . ', ' . $file . '<br />' );
			//Debugger::dump( 'r = ' . $ratio .', h = ' . imagesy($imageThumb) . ', w = ' . imagesx($imageThumb) . ', ' . $portrait . ', ' . $file . '<br />' );
			
			if (intval($height) == imagesy($imageThumb) && intval($width) == imagesx($imageThumb))
			{
				return false;
			}
		}
		
		//echo 'rewriting file...<b />';
		
		$new_image = imagecreatetruecolor($width, $height); 
		
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height
			, imagesx($image)
			, imagesy($image)
			); 
			
		$image = $new_image;
		
		//
		// save the thumb
		//
		$permissions = null;

		switch($image_info["mime"])
		{
			case "image/jpeg":
				$compression = 75;
				imagejpeg($image, $fileThumb, $compression); 
				break;
				
			case "image/gif":
				imagegif($image, $fileThumb); 
				break;
				
			case "image/png":
				imagepng($image, $fileThumb); 
				break;
				
			default: 
				break;
		}
		
		if( $permissions != null) 
		{   
			chmod($fileThumb, $permissions); 
		}
		
		return true;
	}	
}
