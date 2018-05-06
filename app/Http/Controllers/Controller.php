<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use App\Task;
use App\Entry;

define('BODY_PLACEHODER', '[[body]]'); // tag that gets replaced with the body of the template
define('TOUR_PHOTOS_PATH', '/public/img/theme1/tours/');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $viewData = [];
	
	public function __construct ()
	{
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
		//dd($text);
		$text = "";
		
		foreach($lines as $line)
		{
				preg_match('/\[(.*?)\]/', $line, $title);		// replace the chars between []
				preg_match('/\((.*?)\)/', $line, $link);	// replace the chars between ()
				
				if ($line === BODY_PLACEHODER)
				{
					dd($line);
					// this is for the template replacement tag 
					$text .= $line;
				}
				else if (sizeof($title) > 0 && sizeof($link)) // if its a link
				{
					$text .= '<a href=' . $link[1] . ' target="_blank">' . $title[1] . '</a><br/>';
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

    protected function getPhotos(Entry $entry)
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
