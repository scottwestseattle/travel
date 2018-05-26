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
		if (!$this->isAdmin())
             return redirect('/');

		$id = intval($id);
		
    	if ($id > 0 && Auth::check())
        {
			$subfolder = 'tours/' . $id . '/';			
			$path = $this->getPhotosWebPath($subfolder);
			
			//old way, folder based: $photos = $this->getPhotos($subfolder, EXT_JPG);
			$photos = Photo::select()
				->where('user_id', '=', Auth::id())
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $id)
				->orderByRaw('photos.main_flag DESC, photos.created_at ASC')
				->get();
				
			return view('photos.index', ['title' => 'Tour', 'photo_type' => 2, 'id' => $id, 'path' => $path, 'photos' => $photos, 'data' => $this->getViewData()]);	
        }           
        else 
		{
             return redirect('/');
        }
	}
	
	public function sliders()
	{		
		$photos = Photo::select()
			->where('parent_id', '=', 0)
			//->whereNull('parent_id')
			->where('deleted_flag', 0)
			->orderByRaw('photos.id DESC')
			->get();
				
		return view('photos.index', ['title' => 'Slider', 'photo_type' => 1, 'path' => '/img/sliders/', 'photos' => $photos, 'page_title' => 'Photos']);	
	}
	
    public function index()
    {
		if (!$this->isAdmin())
             return redirect('/');

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
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {            
			return view('photos.add', ['id' => $id, 'data' => $this->getViewData()]);
        }           
        else 
		{
             return redirect('/');
        }       
	}
	
    public function create(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
	
    	if (Auth::check())
        {            
			//
			// get file to upload
			//
			$file = $request->file('image');
			
			if (!isset($file))
			{
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', 'Image to upload must be set using the [Browse] button');		
				
				// bad or missing file name
				return view('photos.add', ['id' => $request->parent_id, 'data' => $this->getViewData()]);					
			}
			
			//
			// get and check file extension
			//
			$ext = strtolower($file->getClientOriginalExtension());
			if (isset($ext) && $ext === 'jpg')
			{
				// correct extension
			}
			else
			{
				// bad or missing extension
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', 'Only JPG images can be uploaded');	
				
				return view('photos.add', ['id' => $request->parent_id, 'data' => $this->getViewData()]);					
			}
						
			//
			// get and check new file name
			//
			$filename = $this->getPhotoName($request->filename, $file->getClientOriginalName(), $alt_text_default);
			
			//
			// get and fix alt_text
			//
			$alt_text = trim($request->alt_text);
			if (isset($alt_text) && strlen($alt_text) > 0)
			{
				// alt_text is set
			}
			else
			{
				// alt_text not set, fix it up
				if (isset($alt_text_default) && strlen($alt_text_default) > 0)
				{
					$alt_text = $alt_text_default;
				}
				else
				{
					// alt_text_default not set, so use filename to gen alt_text
					$alt_text = str_replace("-", " ", $filename);	// replace dashes with spaces
					$alt_text = str_replace(".jpg", "", $alt_text);	// remove file extension
				}
			}				
				
			$id = intval($request->parent_id);
			
			if ($id === 0) // for now these are sliders
			{
				// slider photos
				$path = $this->getPhotosFullPath('sliders/');
				
				$redirect = '/photos/sliders';
				$redirect_error = '/photos/add/0';
			}
			else
			{
				// tour photos
				$path = $this->getPhotosFullPath('tours/' . $id . '/');

				$redirect = '/photos/tours/' . $id;
				$redirect_error = '/photos/add/' . $id;				
			}
						
			try 
			{				
				// upload the file
				$request->file('image')->move($path, $filename);
				
				// add the photo record
				$photo = new Photo();
				$photo->filename = $filename;
				$photo->alt_text = $alt_text;
				$photo->location = trim($request->location);
				$photo->main_flag = isset($request->main_flag) ? 1 : 0;
				$photo->parent_id = $id;
				$photo->user_id = Auth::id();
				
				//dd($photo);		
				
				$photo->save();
				
				$request->session()->flash('message.level', 'success');	
				$request->session()->flash('message.content', 'Photo was successfully uploaded!');
				
				return redirect($redirect);
			}
			catch (\Exception $e) 
			{
				if ($e->getCode() == 0)
				{
				}
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
				
				return redirect($redirect_error);
			}					
        }           
        else 
		{
             return redirect('/');
        }            	
    }

    protected function getPhotoName($filename_to, $filename_from, &$alt_text)
    {
		$filename = trim($filename_to); // use $alt_text as a holder
		if (isset($filename) && strlen($filename) > 0)
		{
			//
			// a new file name has been provided, fix it up
			//
			$filename = preg_replace('/[^\da-z ]/i', ' ', $filename);	// replace all non-alphanums with space
			$filename = ucwords($filename);								// cap each word in name
			$alt_text = $filename;										// use this as the default alt_text
			
			$filename = str_replace(" ", "-", $filename);				// replace spaces with dashes
			$filename .= '.jpg';										// add the extension
		}
		else
		{
			//
			// no file name given so use the original file name from the actual file
			//
			$filename = $filename_from;
			
			// fix up the alt text
			$alt_text = preg_replace('/[^\da-z ]/i', ' ', $filename);	// replace all non-alphanums with space
			$alt_text = preg_replace('/.jpg/i', '', $alt_text);			// get rid of the file extension
			$alt_text = ucwords($alt_text);								// cap each word in name
		}
			
		return $filename;
	}

    public function view(Photo $photo)
    {
		$path = '/img/sliders/';
		
		return view('photos.view', ['photo' => $photo, 'path' => $path, 'page_title' => 'Photos - ' . $photo->alt_text]);
	}
	
    public function edit(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {			
			return view('photos.edit', ['photo' => $photo, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {
			$id = intval($request->parent_id);
			if ($id === 0)
			{
				$path_from = base_path() . '/public/img/sliders/';
				$redirect = '/photos/sliders/';
			}
			else
			{
				$path_from = base_path() . '/public/img/tours/' . $id . '/';
				$redirect = '/photos/tours/' . $id;
			}

			$filename = trim($request->filename);
			
			if ($request->filename_orig === $filename)
			{
				// file name not changed
			}
			else
			{
				if (strlen($filename) > 0)
				{
					//
					// file name changed, change the physical file name
					//					
					$path_to = $path_from;
					
					$path_from .= $request->filename_orig;
					
					// get and fix up the new file name
					$filename = $this->getPhotoName($filename, $request->filename_orig, $alt_text_default);
					$path_to .= $filename;

					rename($path_from, $path_to);
				}
				else
				{
					// new file name can't be blank
					$filename = $request->filename_orig;
				}
			}	
			
			//
			// get and fix alt_text
			//
			$alt_text = trim($request->alt_text);
			if (isset($alt_text) && strlen($alt_text) > 0)
			{
				// alt_text is set
			}
			else
			{
				// alt_text not set, fix it up
				if (isset($alt_text_default) && strlen($alt_text_default) > 0)
				{
					$alt_text = $alt_text_default;
				}
				else
				{
					// alt_text_default not set, so use filename to gen alt_text
					$alt_text = str_replace("-", " ", $filename);	// replace dashes with spaces
					$alt_text = str_replace(".jpg", "", $alt_text);	// remove file extension
				}
			}
			
			//
			// update the db record
			//
			$photo->filename = $filename;
			$photo->alt_text = $alt_text;
			$photo->main_flag = isset($request->main_flag) ? 1 : 0;
			$photo->location = trim($request->location);
			$photo->save();
				
			return redirect($redirect); 
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {			
			$path = '';
			if ($this->isSlider($photo))
				$path .= '/img/sliders/';
			else
				$path .= '/img/tours/' . $photo->parent_id . '/';
	
			//dd($path);
	
			return view('photos.confirmdelete', ['photo' => $photo, 'path' => $path, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$redirect = '/';
	
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {			
			// 
			// update the database record
			//
			$photo->deleted_flag = 1;
			$photo->save();	

			//
			// move the file to the deleted folder
			//
			if ($this->isSlider($photo))
			{
				$path_from = base_path() . '/public/img/sliders/';
				$redirect = '/photos/sliders';
			}
			else
			{
				$path_from = base_path() . '/public/img/tours/' . $request->parent_id . '/';
				$redirect = '/photos/tours/' . $request->parent_id;
			}
			
			$path_to = $path_from . 'deleted/';
						
			if (!is_dir($path_to)) 
			{
				// make the folder with read/execute for everybody
				mkdir($path_to, 0755);
			}
			
			$path_from .= $photo->filename;
			$path_to .= $photo->filename;

			try
			{
				rename($path_from, $path_to);
			}
			catch (\Exception $e) 
			{
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
			}
		}
		
		return redirect($redirect);
    }

	protected function isSlider(Photo $photo)
	{
		$id = intval($photo->parent_id);
		
		return ($id === 0);
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
}
