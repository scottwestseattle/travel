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
				
			foreach($photos as $photo)
			{
				$fullPath = $this->getPhotosFullPath('tours/' . $id . '/') . $photo->filename;
				$size = filesize($fullPath);
				$photo['size'] = $size;
			}
				
			return view('photos.index', ['title' => 'Tour', 'photo_type' => 2, 'id' => $id, 'path' => $path, 'photos' => $photos, 'record_id' => $id]);	
        }           
        else 
		{
             return redirect('/');
        }
	}

	public function entries($id)
	{		
		if (!$this->isAdmin())
             return redirect('/');

		$id = intval($id);
		
    	if ($id > 0 && Auth::check())
        {
			$subfolder = 'entries/' . $id . '/';
			$path = $this->getPhotosWebPath($subfolder);
			
			$photos = Photo::select()
				->where('user_id', '=', Auth::id())
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $id)
				->orderByRaw('photos.main_flag DESC, photos.created_at ASC')
				->get();
				
			foreach($photos as $photo)
			{
				$fullPath = $this->getPhotosFullPath('entries/' . $id . '/') . $photo->filename;
				$size = filesize($fullPath);
				$photo['size'] = $size;
			}
				
			return view('photos.index', ['title' => 'Entry', 'photo_type' => 2, 'id' => $id, 'path' => $path, 'photos' => $photos, 'record_id' => $id]);	
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
		
    public function add($type_flag, $parent_id)
    {
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {
			return view('photos.add', ['parent_id' => $parent_id, 'type_flag' => $photo_type]);
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
				return view('photos.add', ['parent_id' => $request->parent_id, 'photo_type' => $request->photo_type]);					
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
				
				return view('photos.add', ['parent_id' => $request->parent_id, 'photo_type' => $request->photo_type]);					
			}
						
			//
			// get and check new file name
			//
			$filename = $this->getPhotoName(trim($request->filename), $file->getClientOriginalName(), $alt_text_default);
			
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
			$photo_type = intval($request->photo_type);
			$subfolder = '';
			
			switch($photo_type)
			{
				case PHOTO_TYPE_SLIDER:
					$subfolder = 'sliders';
					$redirect = '/photos/sliders';
					break;
				case ENTRY_TYPE_ENTRY:
					$subfolder = 'entries';
					$redirect = '/photos/sliders';
					break;
				case ENTRY_TYPE_TOUR:
					$subfolder = 'tours';
					$redirect = '/photos/tours';
					break;
				case ENTRY_TYPE_BLOG:
					$subfolder = 'blogs';
					$redirect = '/photos/blogs';
					break;
				case ENTRY_TYPE_BLOG_ENTRY:
					$subfolder = 'blogs';
					$redirect = '/photos/blogs';
					break;
				case ENTRY_TYPE_ARTICLE:
					$subfolder = 'articles';
					$redirect = '/photos/articles';
					break;
				case ENTRY_TYPE_NOTE:
					$subfolder = 'notes';
					$redirect = '/photos/notes';
					break;
				case ENTRY_TYPE_OTHER:
					$subfolder = 'other';
					$redirect = '/photos/other';
					break;
				default:
					break;
			}
			
			$path = $this->getPhotosFullPath($subfolder . '/');
			$redirect_error = '/photos/add/' . $id;	
			dd($path);
						
			try 
			{
				$tempPath = $path . 'tmp/';
				if (!is_dir($tempPath)) 
				{
					if (!is_dir($path)) 
					{
						mkdir($path, 0755);// make the folder with read/execute for everybody
					}
					
					mkdir($tempPath, 0755);// make the folder with read/execute for everybody
				}
		
				// upload the file
				$request->file('image')->move($tempPath, $filename);
				
				//
				// check the file size in case it needs to be reduced
				//
				$newSize = 0;
				$size = filesize($tempPath . $filename);
				$resized = false;
				if (intval($size) > 2000000) // 2mb limit
				{
					// resize and put it up in the live photo folder
					if ($this->resizeImage($tempPath, $path, $filename, /* new size = */ 750, /* makeOnly = */ true))
					{
						$resized = true;
						$newSize = filesize($path . $filename);
						unlink($tempPath . $filename); // delete the oversized file
					}
					else
					{
						dd('here');
						$request->session()->flash('message.level', 'danger');
						$request->session()->flash('message.content', 'Resizing of image failed');
					}
				}
				else
				{
					// no need to resize, just move it from temp to live photo folder
					rename($tempPath . $filename, $path . $filename);
				}
								
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
				
				if ($resized)
					$request->session()->flash('message.content', 'Photo was successfully uploaded and resized from ' . number_format($size) . ' bytes to ' . number_format($newSize) . ' bytes');
				else
					$request->session()->flash('message.content', 'Photo was successfully uploaded');
				
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
	
	private function resizeImage($fromPath, $toPath, $filename, $heightNew, $makeOnly)
	{
		if (!is_dir($toPath)) 
			mkdir($toPath, 0755);// make the folder with read/execute for everybody
		
		//
		// get image info
		//
		//Debugger::dump('from: ' . $toPath);die;	
			
		$file = $fromPath;
				
		if (!$this->endsWith($fromPath, '/'))
			$file .= '/';
		
		$file .= $filename;
		
		$fileThumb = $toPath . '/' . $filename;
		
		if ($makeOnly && file_exists($fileThumb))
			return false; 
			
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

		$fileThumb = $toPath . '/' . $filename;

		if (file_exists($fileThumb))
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
