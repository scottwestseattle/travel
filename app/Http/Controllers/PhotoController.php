<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PhotoController extends Controller
{
	public function tours($id)
	{		
    	if (Auth::check())
        {
			$subfolder = 'tours/' . $id . '/';
			
			$photos = $this->getPhotos($subfolder, EXT_JPG);
						
			return view('photos.index', ['id' => $id, 'path' => $this->getPhotosWebPath($subfolder), 'photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
	}
	
	public function sliders()
	{		
    	if (Auth::check())
        {
			$photos = $this->getSliders();
			
			return view('photos.index', ['path' => PHOTOS_WEB_PATH, 'photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
	}

    protected function getSliders()
    {
		$path = base_path() . PHOTOS_FULL_PATH;
		//dd($path);
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
		
    public function add($id)
    {
    	if (Auth::check())
        {            
			return view('photos.add', ['id' => $id, 'data' => $this->getViewData()]);
        }           
        else 
		{
             return redirect('/');
        }       
	}
	
    public function create(Request $request, $id)
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
				return view('photos.add', ['id' => $id, 'data' => $this->getViewData()]);					
			}
						
			//
			// get and check new file name
			//
			$name = trim($request->name);
			if (isset($name) && strlen($name) > 0)
			{
				$name = preg_replace('/[^\da-z ]/i', ' ', $name);	// remove all non-alphanums
				$name = ucwords($name);								// cap each word in name
				$name = str_replace(" ", "-", $name);				// replace spaces with dashes
			}
			else
			{
				// no file name given so use original name
				$name = $file->getClientOriginalName();
			}

			$name .= '.' . $ext;
							
			$path = $this->getPhotosFullPath('tours/' . $id . '/');
			
			try 
			{
				$request->file('image')->move($path, $name);
				
				//$request->session()->flash('message.level', 'success');	
				//$request->session()->flash('message.content', 'Photo was successfully added!');
			}
			catch (\Exception $e) 
			{
				dd($e.getMessage());
				//$request->session()->flash('message.level', 'danger');
				//$request->session()->flash('message.content', $e.getMessage());		
			}			
						
			return redirect('/photos/tours/' . $id);
        }           
        else 
		{
             return redirect('/');
        }            	
    }

    public function view()
    {
		$photos = $this->getPhotos();
						
		return view('entries.view', ['data' => $this->getViewData(), 'photos' => $photos]);
	}
	
    public function edit()
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
