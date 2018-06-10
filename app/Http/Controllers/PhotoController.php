<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Photo;
use DB;

class PhotoController extends Controller
{
	public function tours(Request $request, $id)
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
				->where('site_id', SITE_ID)
				->where('user_id', '=', Auth::id())
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $id)
				->orderByRaw('photos.main_flag DESC, photos.created_at ASC')
				->get();
				
			foreach($photos as $photo)
			{
				// get photo size info
				$fullPath = $this->getPhotosFullPath('tours/' . $id . '/') . $photo->filename;
				try
				{
					$size = filesize($fullPath);
					$photo['size'] = $size;
				}
				catch (\Exception $e) 
				{
					$request->session()->flash('message.level', 'danger');
					$request->session()->flash('message.content', $e->getMessage());
					//return redirect('/activities/indexadmin');
				}

			}
				
			return view('photos.index', ['title' => 'Tour', 'id' => $id, 'path' => $path, 'photos' => $photos, 'record_id' => $id]);	
        }           
        else 
		{
             return redirect('/');
        }
	}

	public function entries($parent_id)
	{			
		if (!$this->isAdmin())
             return redirect('/');

		$parent_id = intval($parent_id);		 
		
    	if ($parent_id > 0 && Auth::check())
        {
			$subfolder = PHOTO_ENTRY_FOLDER . '/' . $parent_id . '/';			
			$path = $this->getPhotosWebPath($subfolder);
			
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('user_id', '=', Auth::id())
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $parent_id)
				->orderByRaw('photos.main_flag DESC, photos.created_at ASC')
				->get();
				
			foreach($photos as $photo)
			{
				$fullPath = $this->getPhotosFullPath($subfolder) . $photo->filename;
				$size = filesize($fullPath);
				$photo['size'] = $size;
			}
				
			return view('photos.index', ['id' => $parent_id, 'path' => $path, 'photos' => $photos, 'record_id' => $parent_id]);	
        }           
        else 
		{
             return redirect('/');
        }
	}
	
	public function sliders()
	{			
		$q = '
			SELECT id, filename, alt_text, location, main_flag
				, CONCAT(alt_text, " - ", location) as photo_title
				, CONCAT("' . PHOTO_SLIDER_PATH . '") as path
			FROM photos
			WHERE 1=1
				AND deleted_flag = 0
				AND site_id = ?
				AND (parent_id is null OR parent_id = 0)
			ORDER BY id DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [SITE_ID]);
		
		$vdata = $this->getViewData([
			'title' => 'Slider', 
			'photos' => $records, 		
		]);
						
		return view('photos.sliders', $vdata);	
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
	
    public function indexadmin()
    {
		if (!$this->isAdmin())
             return redirect('/');

		if (Auth::check())
        {
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '>', 0)
				->get();
			
			return view('photos.indexadmin', ['photos' => $photos, 'path' => $this->getPhotoPath()]);	
        }           
        else 
		{
             return redirect('/');
        }
    }
		
    public function add($parent_id = 0)
    {
		if (!$this->isAdmin())
			return redirect('/');

    	if (Auth::check())
        {            
			return view('photos.add', ['parent_id' => $parent_id]);					
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
				return view('photos.add', ['parent_id' => $request->parent_id]);					
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
				
				return view('photos.add', ['parent_id' => $request->parent_id]);					
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
			
			if ($id === 0) // for now these are sliders
			{
				// slider photos
				$path = $this->getPhotosFullPath(PHOTO_SLIDER_FOLDER . '/');
				
				$redirect = '/photos/' . PHOTO_SLIDER_FOLDER;
				$redirect_error = '/photos/add/0';
			}
			else
			{
				// tour photos
				$path = $this->getPhotosFullPath(PHOTO_ENTRY_FOLDER . '/' . $id . '/');

				$redirect = '/photos/' . PHOTO_ENTRY_FOLDER . '/' . $id;
				$redirect_error = '/photos/add/' . $id;				
			}
						
			try 
			{
				$tempPath = $path . PHOTO_TMP_FOLDER . '/';
				if (!is_dir($tempPath)) 
				{
					$image_folder = $this->getPhotosFullPath(PHOTO_ENTRY_FOLDER);
					if (!is_dir($image_folder))
					{
						// make the main folder
						mkdir($image_folder, 0755);						
					}

					if (!is_dir($path))
					{
						// make the entry folder
						mkdir($path, 0755);
					}					
					
					mkdir($tempPath, 0755);// make the tmp folder with read/execute for everybody
				}
		
				// upload the file
				$request->file('image')->move($tempPath, $filename);
				
				//
				// check the file size in case it needs to be reduced
				//
				$newSize = 0;
				$size = filesize($tempPath . $filename);
				$resized = false;
				if ($id > 0 && intval($size) > 2000000) // 2mb limit for non-sliders only
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
		$path = $this->getPhotoPath($photo);
		
		$vdata = $this->getViewData([
			'photo' => $photo, 'path' => $path, 'page_title' => 'Photos - ' . $photo->alt_text
		]);		
		
		return view('photos.view', $vdata);
	}
	
    public function edit(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {			
			$path = '';
			if (intval($photo->parent_id) === 0)
			{
				$path = '/img/' . PHOTO_SLIDER_FOLDER . '/' . $photo->filename;
			}
			else
			{
				$path = '/img/' . PHOTO_ENTRY_FOLDER . '/' . $photo->parent_id . '/' . $photo->filename;
			}
	
			return view('photos.edit', ['record' => $photo, 'path' => $path]);							
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
				$path_from = base_path() . '/public/img/' . PHOTO_SLIDER_FOLDER . '/';
				$redirect = '/photos/' . PHOTO_SLIDER_FOLDER . '/';
			}
			else
			{
				$path_from = base_path() . '/public/img/' . PHOTO_ENTRY_FOLDER . '/' . $id . '/';
				$redirect = '/photos/' . PHOTO_ENTRY_FOLDER . '/' . $id;
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
				$path .= '/img/' . PHOTO_SLIDER_FOLDER . '/';
			else
				$path .= '/img/' . PHOTO_ENTRY_FOLDER . '/' . $photo->parent_id . '/';
	
			return view('photos.confirmdelete', ['photo' => $photo, 'path' => $path]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Photo $photo)
    {		
		$redirect = null;
		$message = null;
		$messageLevel = null;
		
		$this->deletePhoto($photo, $redirect, $message, $messageLevel);
		
		$request->session()->flash('message.level', $messageLevel);
		$request->session()->flash('message.content', $message);
		
		return redirect($redirect);
    }
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
}
