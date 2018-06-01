<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use App\Task;
use App\Entry;
use App\Visitor;

define('BODY_PLACEHODER', '[[body]]'); // tag that gets replaced with the body of the template
define('TOUR_PHOTOS_PATH', '/public/img/tours/');
define('SLIDER_PHOTOS_PATH', '/public/img/sliders/');
define('PHOTOS_FULL_PATH', '/public/img/');
define('PHOTOS_WEB_PATH', '/img/');
define('EXT_JPG', '.jpg');
define('USER_UNCONFIRMED', 0);		// user unconfirmed
define('USER_CONFIRMED', 10);		// user confirmed
define('USER_WRITER', 20);			// article/tour write
define('USER_EDITOR', 30);			// content editor
define('USER_SITE_ADMIN', 100);		// user site admin
define('USER_SUPER_ADMIN', 1000);	// user super admin

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

// -1=not set, 1=entry, 2=tour/hike, 3=blog, 4=blog entry, 5=article, 6=note, 7=other 	
define('ENTRY_TYPE_NOTSET', 	-1);
define('ENTRY_TYPE_ENTRY', 		1);
define('ENTRY_TYPE_TOUR', 		2);
define('ENTRY_TYPE_BLOG', 		3);
define('ENTRY_TYPE_BLOG_ENTRY', 4);
define('ENTRY_TYPE_ARTICLE', 	5);
define('ENTRY_TYPE_NOTE', 		6);
define('ENTRY_TYPE_OTHER',		7);

// -1=not set, 0=slider, 1=entry, 2=tour/hike, 3=blog, 4=blog entry, 5=article, 6=note, 7=other 
define('PHOTO_TYPE_NOTSET', 	-1);
define('PHOTO_TYPE_SLIDER', 	0);

define('PHOTO_SLIDER_FOLDER', 'sliders');
define('PHOTO_ENTRY_FOLDER', 'entries');
define('PHOTO_TMP_FOLDER', 'tmp');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $viewData = [];
	
	public function __construct ()
	{		
	}

	protected function getSiteId()
	{
		return 1;
	}
		
	protected function getVisitorIp()
	{
		$ip = null;
		
		if (!empty($_SERVER["HTTP_CLIENT_IP"]))
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}	
		
		return $ip;
	}
	
	protected function getVisitorInfo(&$host, &$referrer, &$userAgent)
	{
		//
		// get visitor info
		//
		$ip = $this->getVisitorIp();
						
		$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);		

		$referrer = null;
		if (array_key_exists("HTTP_REFERER", $_SERVER))
			$referrer = $_SERVER["HTTP_REFERER"];

		$userAgent = null;
		if (array_key_exists("HTTP_USER_AGENT", $_SERVER))
			$userAgent = $_SERVER["HTTP_USER_AGENT"];

		return $ip;
	}
	
	protected function saveVisitor()
	{
		$save = false;
		$host = null;
		$referrer = null;
		$userAgent = null;
		
		$ip = $this->getVisitorInfo($host, $referrer, $userAgent);
		
		$visitor = Visitor::select()
			->where('ip_address', '=', $ip)
			->where('deleted_flag', 0)
			->first();
			
		if (!isset($visitor)) // new visitor
		{
			$visitor = new Visitor();
			$visitor->ip_address = $ip;	
		}
		
		$visitor->visit_count++;			
		$visitor->site_id = 1;
		$visitor->host_name = $this->trunc($host, VISITOR_MAX_LENGTH);
		$visitor->user_agent = $this->trunc($userAgent, VISITOR_MAX_LENGTH);
		$visitor->referrer = $this->trunc($referrer, VISITOR_MAX_LENGTH);
		
		$visitor->save();		
	}	

	protected function trunc($string, $length)
	{
		$ellipsis = '...';
		$newLength = $length - strlen($ellipsis);
		$string = (strlen($string) > $length) ? substr($string, 0, $newLength) . $ellipsis : $string;
		//dd($string);
		
		return $string;
	}	
	
	protected function isNewVisitor()
	{
		$ip = $this->getVisitorIp();
		
		$visitor = Visitor::select()
			->where('ip_address', '=', $ip)
			->where('deleted_flag', 0)
			->first();

		//dd($visitor);
		
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
	
	protected function getViewData()
	{
		$taskCount = Task::select()
			->where('user_id', '=', Auth::id())
			->count();

		$this->viewData['taskCount'] = $taskCount;
		
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
		//dd($path);
		$files = scandir($path);
		$photos = [];
		foreach($files as $file)
		{
			if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
			{
				if (/* $this->startsWith($file, 'slider') && */ $this->endsWith($file, '.jpg'))
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
			
		if (!$this->endsWith($path, '/'))
			$path .= '/';
			
		//dd($path);
		
		return $path;
	}
	
    protected function getPhotosFullPath($subfolder = '')
    {
		$path = base_path() . PHOTOS_FULL_PATH;
		
		if (strlen($subfolder) > 0)
			$path .= $subfolder;
			
		if (!$this->endsWith($path, '/'))
			$path .= '/';
			
		//dd($path);
		
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
					if ($this->endsWith($file, $ext))
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
		
		//dd($photos);
			
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
	
	protected function endsWith($haystack, $needle)
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
}
