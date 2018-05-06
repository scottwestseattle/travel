<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

define('PHOTOS_PATH', '/public/img/theme1/');

class PhotoController extends Controller
{
	private $path = PHOTOS_PATH;
	
	public function sliders()
	{		
    	if (Auth::check())
        {
			$photos = $this->getSliders();
			
			return view('photos.index', ['photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
	}

    protected function getSliders()
    {
		$path = base_path() . $this->path;
		$files = scandir($path);						
		foreach($files as $file)
		{
			if ($file != '..' && $file != '.' && !is_dir($path . '/' . $file))
			{
				if ($this->startsWith($file, 'slider') && $this->endsWith($file, '.jpg'))
				{
					$photos[] = $file;					
				}
			}
		}
				
		return $photos;
    }	
	
    public function index()
    {
    	if (Auth::check())
        {
			$photos = [];
			
			return view('photos.index', ['photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
    }
		
    public function add()
    {
    	if (Auth::check())
        {            
			return view('entries.add', ['data' => $this->getViewData()]);
        }           
        else 
		{
             return redirect('/');
        }       
	}
	
    public function create(Request $request)
    {		
    	if (Auth::check())
        {            
			//
			// get file to upload
			//
			$file = $request->file('image');
			if (!isset($file))
			{
				// bad or missing file name
				return view('photos.add', ['data' => $this->getViewData()]);	
			}
			
			//
			// get and check file extension
			//
			$ext = strtolower($file->getClientOriginalExtension());
			if (isset($ext) && $ext === 'jpg')
			{
			}
			else
			{
				// bad or missing extension
				return view('entries.upload', ['entry' => $entry, 'data' => $this->getViewData()]);					
			}
						
			//
			// get and check new file name
			//
			$name = trim($request->name);
			if (isset($name) && strlen($name) > 0)
			{
				$name = preg_replace('/[^\da-z ]/i', ' ', $name); // remove all non-alphanums
				$name = str_replace(" ", "-", $name);			// replace spaces with dashes
			}
			else
			{
				// no file name given so name it with timestamp
				$name = date("Ymd-His");
			}

			$name .= '.' . $ext;
							
			$path = base_path() . TOUR_PHOTOS_PATH . $entry->id;
			
			//dd($name);
			
			$request->file('image')->move($path, $name);
						
			return redirect('/entries/view/' . $entry->id);
        }           
        else 
		{
             return redirect('/');
        }            	
    }

    public function view()
    {
		$photos = $this->getPhotos();
						
		return view('entries.view', ['entry' => $entry, 'data' => $this->getViewData(), 'photos' => $photos]);
	}
	
    public function edit(Request $request)
    {
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			return view('photos.edit', ['photo' => $photo, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			//dd($request);
							
			return redirect('/photo/view/'); 
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Request $request)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {			
			return view('photos.delete', ['data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			//$entry->delete();			
		}
		
		return redirect('/photos/index');
    }	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
}
