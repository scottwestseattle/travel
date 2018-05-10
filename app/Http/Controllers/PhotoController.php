<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Photo;
use DB;

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
			//old photo driven: $photos = $this->getSliders();
			$photos = Photo::select()
				->where('user_id', '=', Auth::id())
				->where('deleted_flag', '<>', 1)
				->orderByRaw('photos.id DESC')
				->get();
			
			return view('photos.index', ['path' => SLIDER_PHOTOS_PATH, 'photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
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
		
    public function add($id = 0)
    {
    	if (Auth::check())
        {            
			return view('photos.add', ['id' => isset($id) ? $id : 0, 'data' => $this->getViewData()]);
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
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', 'Image to upload not set');		
				
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
			$name_fixed = trim($request->filename);
			$name = $name_fixed;
			if (isset($name_fixed) && strlen($name_fixed) > 0)
			{
				$name_fixed = preg_replace('/[^\da-z ]/i', ' ', $name_fixed);	// replace all non-alphanums with space
				$name_fixed = ucwords($name_fixed);								// cap each word in name
				$name = str_replace(" ", "-", $name_fixed);						// replace spaces with dashes
				$name .= '.' . $ext;											// add the extension
			}
			else
			{
				// no file name given so use original name
				$name = $file->getClientOriginalName();
				$name_fixed = $name;
			}
			
			//
			// alt text is optional. if not included, use the file name
			//
			$alt_text = trim($request->alt_text);
			if (isset($alt_text) && strlen($alt_text) > 0)
			{
				// use it as is
			}
			else
			{
				// use the photo name instead
				$alt_text = $name_fixed;	// use the version without the dashes
			}					
					
			$id = isset($id) ? intval($id) : 0;
			
			if ($id === 0) // for now these are sliders
			{
				// slider photos
				$path = $this->getPhotosFullPath('sliders/');
				$redirect = '/photos/sliders';
			}
			else
			{
				// tour photos
				$path = $this->getPhotosFullPath('tours/' . $id . '/');
				$redirect = '/photos/tours/' . $id;				
			}
						
			try 
			{
				// upload the file
				$request->file('image')->move($path, $name);
				
				// add the photo record
				$photo = new Photo();
				$photo->filename = $name;
				$photo->alt_text = $alt_text;
				$photo->location = trim($request->location);
				$photo->user_id = Auth::id();
				
				//dd($photo);		
				
				$photo->save();
				
				//$request->session()->flash('message.level', 'success');	
				//$request->session()->flash('message.content', 'Photo was successfully added!');
			}
			catch (\Exception $e) 
			{
				dd($e->getMessage());
				//$request->session()->flash('message.level', 'danger');
				//$request->session()->flash('message.content', $e.getMessage());		
			}			
						
			return redirect($redirect);
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
	
    public function confirmdelete($id = 0)
    {	
		dd($id);
    	if (Auth::check() /* && Auth::user()->id == $entry->user_id */)
        {			
			return view('photos.confirmdelete', ['id' => $id, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete($id = 0)
    {	
    	if (Auth::check() /* && Auth::user()->id == $entry->user_id */)
        {
			//$entry->delete();			
		}
		
		return redirect('/photos/index');
    }	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
}
