<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Photo;
use App\Event;
use App\Entry;
use App\Transaction;

define('PREFIX', 'photos');
define('LOG_MODEL', 'photos');
define('TITLE', 'Photos');

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
				
			$vdata = $this->getViewData([
				'id' => $id, 
				'path' => $path, 
				'photos' => $photos, 
				'record_id' => $id,
			]);
				
			return view('photos.index', $vdata);
        }           
        else 
		{
             return redirect('/');
        }
	}

	public function entries($parent_id, $type_flag = PHOTO_TYPE_ENTRY)
	{			
		if (!$this->isAdmin())
             return redirect('/');

		$parent_id = intval($parent_id);		 
		
    	if ($parent_id > 0) // if parent_id is set and is not a slider
        {
			$info = Controller::getPhotoInfo($type_flag);
			$folder = $info['folder'];
			$type = $info['type'];

			$subfolder = $folder . '/' . $parent_id . '/';			
			$path = $this->getPhotosWebPath($subfolder);
			$redirect = null;
			
			if ($type_flag == 2)
			{
				$entry = Transaction::select()
					->where('deleted_flag', 0)
					->where('id', $parent_id)
					->first();

				$redirect = '/transactions/filter';
				$title = isset($entry) ? $entry->description : 'No Title';
			}			
			else
			{
				$entry = Entry::select()
					->where('deleted_flag', 0)
					->where('id', $parent_id)
					->first();
				
				$title = isset($entry) ? $entry->title : 'No Title';
			}
						
			$photos = Photo::select()
				->where('deleted_flag', 0)
				->where('parent_id', '=', $parent_id)
				->orderByRaw('photos.main_flag DESC, photos.gallery_flag DESC, photos.id DESC')
				->get();
				
			foreach($photos as $photo)
			{
				if (!isset($photo->permalink))
					$photo->permalink = str_replace(".jpg", "", $photo->filename);
			}
				
			$galleries = Photo::getGalleryMenuOptions();

			$dates = null;
			//if (isset($photo->display_date))
			//	$dates = Controller::getDateControlSelectedDate($photo->display_date);

			$vdata = $this->getViewData([
				'id' => $parent_id, 
				'path' => $path, 
				'photos' => $photos, 
				'record_id' => $parent_id,
				'type_flag' => $type_flag,
				'type' => $type,
				'entry' => $entry,
				'galleries' => $galleries,
				'record_title' => $title,
				'redirect' => $redirect,
				'dates' => Controller::getDateControlDates(),
				'filter' => $dates,
			]);				
				
			return view('photos.index', $vdata);
        }           
        else 
		{
             return redirect('/');
        }
	}

	// the original way to do it where gallery photos is not enforced
	public function direct($parent_id, $type_flag = PHOTO_TYPE_ENTRY)
	{			
		if (!$this->isAdmin())
             return redirect('/');

		$parent_id = intval($parent_id);		 
		
    	if ($parent_id > 0) // if parent_id is set and is not a slider
        {
			$info = Controller::getPhotoInfo($type_flag);
			$folder = $info['folder'];
			$type = $info['type'];

			$subfolder = $folder . '/' . $parent_id . '/';			
			$path = $this->getPhotosWebPath($subfolder);
			$redirect = null;
			
			if ($type_flag == 2)
			{
				$entry = Transaction::select()
					->where('deleted_flag', 0)
					->where('id', $parent_id)
					->first();

				$redirect = '/transactions/filter';
				$title = isset($entry) ? $entry->description : 'No Title';
			}			
			else
			{
				$entry = Entry::select()
					->where('deleted_flag', 0)
					->where('id', $parent_id)
					->first();
				
				$title = isset($entry) ? $entry->title : 'No Title';
			}
						
			$photos = Photo::select()
				->where('deleted_flag', 0)
				->where('parent_id', '=', $parent_id)
				->orderByRaw('photos.main_flag DESC, photos.gallery_flag DESC, photos.id DESC')
				->get();
				
			foreach($photos as $photo)
			{
				if (!isset($photo->permalink))
					$photo->permalink = str_replace(".jpg", "", $photo->filename);
			}
				
			$galleries = Photo::getGalleryMenuOptions();

			$dates = null;
			//if (isset($photo->display_date))
			//	$dates = Controller::getDateControlSelectedDate($photo->display_date);

			$vdata = $this->getViewData([
				'id' => $parent_id, 
				'path' => $path, 
				'photos' => $photos, 
				'record_id' => $parent_id,
				'type_flag' => $type_flag,
				'type' => $type,
				'entry' => $entry,
				'galleries' => $galleries,
				'record_title' => $title,
				'redirect' => $redirect,
				'dates' => Controller::getDateControlDates(),
				'filter' => $dates,
			]);				
				
			return view('photos.direct', $vdata);
        }           
        else 
		{
             return redirect('/');
        }
	}
	
	public function entriesupdate(Request $request)
	{	
		$display_date = Controller::getSelectedDate($request);

		Photo::setDisplayDate($request->parent_id, $display_date);
		
        return redirect('/photos/entries/' . $request->parent_id);
	}

	public function sliders(Request $request, $all = false)
	{
		$all = isset($all) && $all !== false;
		
		if ($this->isAdmin())
             return $this->slidersAdmin($request, $all);
	
		$photo = Photo::getFirst(/* parent_id = */ 0);

		if (!isset($photo))
		{
			$msg = 'First Slider Not Found';

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);

			$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			
			Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_VIEW, /* title = */ $msg, null, null, null, null, $link);			
			
			return back();
		}
		
		return $this->permalink($request, null, $photo->id);
	}
			
	public function slidersAdmin(Request $request , $all = false)
	{					
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_SLIDERS);

		$q = '
			SELECT id, filename, permalink, alt_text, location, main_flag, parent_id, site_id 
				, CONCAT(alt_text, " - ", location) as photo_title
				, CONCAT("' . PHOTO_SLIDER_PATH . '") as path
			FROM photos
			WHERE 1=1
				AND deleted_flag = 0
				AND (parent_id is null OR parent_id = 0)
			ORDER BY id DESC
		';
		
		if (!$all)
			$q .= ' LIMIT 10 ';
		
		// get the list with the location included
		$records = DB::select($q);
		
		$sliderPath = '/img/sliders/';
		$sliderPath = count($records) > 0 ? Controller::getPhotoPathRemote($sliderPath, $records[0]->site_id) : $sliderPath;
		
		$vdata = $this->getViewData([
			'photos' => $records, 
			'page_title' => __('ui.Featured Photos'),
			'slider_path' => $sliderPath,
		]);
						
		return view('photos.sliders', $vdata);	
	}

	// does a flow view of the sliders
	public function featured()
	{			
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_SLIDERS);

		$q = '
			SELECT id, filename, alt_text, location, main_flag, parent_id, site_id 
				, CONCAT(alt_text, " - ", location) as photo_title
				, CONCAT("' . PHOTO_SLIDER_PATH . '") as path
			FROM photos
			WHERE 1=1
				AND deleted_flag = 0
				AND (parent_id is null OR parent_id = 0)
			ORDER BY id DESC
		';
		
		// get the list with the location included
		$records = DB::select($q, [SITE_ID]);
		
		$sliderPath = '/img/sliders/';
		$sliderPath = count($records) > 0 ? Controller::getPhotoPathRemote($sliderPath, $records[0]->site_id) : $sliderPath;
		
		$vdata = $this->getViewData([
			'photos' => $records, 
			'page_title' => __('ui.Featured Photos'),
			'slider_path' => $sliderPath,
		]);
						
		return view('photos.featured', $vdata);	
	}
	
    public function indexadmin()
    {
		if (!$this->isAdmin())
             return redirect('/');

		if (Auth::check())
        {
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('parent_id', '>', 0)
				->where('type_flag', 1)
				->get();
			
			$vdata = $this->getViewData([
				'photos' => $photos,
				'path' => Controller::getPhotoPath(),
			]);
			
			return view('photos.indexadmin', $vdata);
        }           
        else 
		{
             return redirect('/');
        }
    }
		
    public function add($type_flag, $parent_id = 0)
    {
		if (!$this->isAdmin())
			return redirect('/');
           
		$info = Controller::getPhotoInfoPath($type_flag, $parent_id);

		$type = $info['type'];
		
		$photos = null;
		
		if (!Controller::isSlider($type_flag))
		{
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('parent_id', $parent_id)
				->get();
		}
		
		if (isset($loc) && $loc != '')
		{
			$location = $loc;
		}
		else
		{
			// check if location has been saved in the session so we can pre-populate it
			$location = session('location', '');
		}

		$vdata = $this->getViewData([
			'parent_id' => $parent_id,
			'type_flag' => $type_flag,
			'type' => $type,
			'photos' => $photos,
			'path' => $info['path'],
			'location' => $location,
		]);

		return view('photos.add', $vdata);      
	}
	
    public function create(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			 
		$type = Controller::getPhotoInfo($request->type_flag)['type'];
			 
		$vdata = $this->getViewData([
			'parent_id' => $request->parent_id,
			'type_flag' => $request->type_flag,
			'type' => $type,
			'location' => $request->location,
		]);	
		
 		//
		// get file to upload
		//
		$file = $request->file('image');

		if (!isset($file))
		{
			$msg = 'Image to upload must be set using the [Browse] button';
			
			Event::logError(LOG_MODEL, LOG_ACTION_ADD, /* title = */ $msg);			

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);		
			
			return view('photos.add', $vdata);
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
			$msg = 'Only JPG images can be uploaded';
			
			Event::logError(LOG_MODEL, LOG_ACTION_ADD, /* title = */ $msg);			

			// bad or missing extension
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);	

			return view('photos.add', $vdata);
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
		
		$type_flag = isset($request->type_flag) ? intval($request->type_flag) : PHOTO_TYPE_NOTSET;
		$redirect = '/photos';
		$redirect_error = '/photos/add';
		$path = '/img/';
		$folder = '';
		
		switch($type_flag)
		{
			case PHOTO_TYPE_SLIDER:
			case PHOTO_TYPE_SLIDER_HORIZONTAL_ONLY:
			case PHOTO_TYPE_SLIDER_VERTICAL_ONLY:
				$path = $this->getPhotosFullPath(PHOTO_SLIDER_FOLDER . '/');
				$redirect = '/photos/' . PHOTO_SLIDER_FOLDER;
				$redirect_error = '/photos/add/' . PHOTO_SLIDER_FOLDER;
				$folder = PHOTO_SLIDER_FOLDER;
				break;
			case PHOTO_TYPE_ENTRY:
				$path = $this->getPhotosFullPath(PHOTO_ENTRY_FOLDER . '/' . $id . '/');
				$redirect = '/photos/' . PHOTO_ENTRY_FOLDER . '/' . $id . '/' . $type_flag;
				$redirect_error = '/photos/add/' . PHOTO_ENTRY_FOLDER . '/' . $id;				
				$folder = PHOTO_ENTRY_FOLDER;
				break;
			case PHOTO_TYPE_RECEIPT:
				$path = $this->getPhotosFullPath(PHOTO_RECEIPT_FOLDER . '/' . $id . '/');
				$redirect = '/photos/direct/' . $id . '/' . $type_flag;
				$redirect_error = '/photos/direct/' . $id . '/' . $type_flag;				
				$folder = PHOTO_RECEIPT_FOLDER;
				break;
			default:
				break;
		}
					
		try 
		{
			$tempPath = $path . PHOTO_TMP_FOLDER . '/';
			if (!is_dir($tempPath)) 
			{
				$image_folder = $this->getPhotosFullPath($folder);
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
			if ($size == 0)
			{
				$msg = 'Error uploading file: ' . $tempPath . $filename . ', uploaded size = 0';
				
				Event::logError(LOG_MODEL, LOG_ACTION_ADD, /* title = */ $msg);			

				// bad or missing extension
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);	

				return redirect($redirect_error);
			}
			
			$resized = false;
	
			$filenameUnique = Controller::getUniqueFilename($path, $filename);
			$duplicate = ($filenameUnique != $filename); // filename had to be changed to make it unique
			
			if ($id > 0 && intval($size) > 2000000) // 2mb limit for non-sliders only
			{
				// resize and put it up in the live photo folder
				if (Controller::resizeImage($tempPath, $path, $filename, $filenameUnique, /* new height = */ 1500))
				{
					$resized = true;
					$newSize = filesize($path . $filename);
					unlink($tempPath . $filename); // delete the oversized file
					
					//Event::logAdd(LOG_MODEL, $tempPath . $filename, 'file resized to:' . $newSize, 0);			
				}
				else
				{
					$msg = 'Resizing of image failed';
					
					Event::logError(LOG_MODEL, LOG_ACTION_ADD, /* title = */ $msg);			

					$request->session()->flash('message.level', 'danger');
					$request->session()->flash('message.content', $msg);
				}
			}
			else
			{
				// no need to resize, just move it from temp to live photo folder
				rename($tempPath . $filename, $path . $filenameUnique);
				$size = filesize($path . $filenameUnique);
				//Event::logAdd(LOG_MODEL, $tempPath . $filename, 'file moved to:' . $path . $filenameUnique . ', final size = ' . $size, 0);			
			}
			
			$filename = $filenameUnique;
							
			// add the photo record
			$photo = new Photo();
			$photo->site_id = SITE_ID;
			$photo->filename = $filename;
			$photo->permalink = str_replace(".jpg", "", $filename);
			$photo->alt_text = $alt_text;
			$photo->gallery_flag = isset($request->gallery_flag) ? 1 : 0;
			$photo->parent_id = $id;
			$photo->user_id = Auth::id();
			$photo->type_flag = $type_flag;

			// save the location for next time
			$photo->location = trim($request->location);
			session(['location' => $photo->location]); 
			
			// if photo is being set to main, unset any other main photo
			if (isset($request->main_flag))
			{
				Photo::clearMainPhoto($photo->parent_id);
			}
			$photo->main_flag = isset($request->main_flag) ? 1 : 0;
						
			$photo->save();
			
			$msgDuplicate = ($duplicate) ? 'DUPLICATE ' : '';

			$request->session()->flash('message.level', $msgDuplicate ? 'danger' : 'success');	
						
			if ($resized)
			{
				$msg = $msgDuplicate . 'Photo was successfully uploaded and resized from ' . number_format($size) . ' bytes to ' . number_format($newSize) . ' bytes';

				Event::logAdd(LOG_MODEL, $photo->filename, $msg, $photo->id);
				
				$request->session()->flash('message.content', $msg);
			}
			else
			{
				$msg = $msgDuplicate . 'Photo was successfully uploaded';

				Event::logAdd(LOG_MODEL, $photo->filename, $msg, $photo->id);

				$request->session()->flash('message.content', $msg);
			}
			
			return redirect($redirect);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'filename = ' . (isset($photo) ? $photo->filename : 'no name'), null, $e->getMessage());

			if ($e->getCode() == 0)
			{
			}
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());
			
			return redirect($redirect_error);
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
			$filename = str_replace('.jpg', '', strtolower($filename));
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
	
    public function view(Request $request, Photo $photo)
    {    	
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW, $photo->id);

		// check for orphan photo
		if ($photo->parent_id > 0)
		{
			$entry = Entry::select()
				->where('deleted_flag', 0)
				->where('id', $photo->parent_id)
				->first();
			
			if (!isset($entry))
			{
				$msg = 'Invalid Photo Found: ' . $photo->id;

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
		
				Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_VIEW, /* title = */ $msg);			
		
				return redirect('/galleries');
			}
		}
		
		$path = Controller::getPhotoPath($photo);
		$path = Controller::getPhotoPathRemote($path, $photo->site_id);
		
		$next = Photo::getNextPrev($photo->parent_id, $photo->id);
		$prev = Photo::getNextPrev($photo->parent_id, $photo->id, /* next = */ false);
		
		if (isset($request->return_url))
			$referrer = $request->return_url;
		else
			$referrer = array_key_exists("HTTP_REFERER", $_SERVER) ? $_SERVER["HTTP_REFERER"] : '/galleries';
			
		$vdata = $this->getViewData([
			'photo' => $photo,
			'path' => $path, 
			'page_title' => 'Photo of ' . $photo->alt_text,
			'next' => $next,
			'prev' => $prev,
			'return_url' => $referrer,
		]);		
		
		return view('photos.view', $vdata);
	}

    public function gallery(Photo $photo)
    {  	
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW, $photo->id);

		$path = Controller::getPhotoPath($photo);
		$path = Controller::getPhotoPathRemote($path, $photo->site_id);
		
		$next = Photo::getNextPrev($photo->parent_id, $photo->id);
		$prev = Photo::getNextPrev($photo->parent_id, $photo->id, /* next = */ false);
		$first = Photo::getFirst($photo->parent_id);

		$vdata = $this->getViewData([
			'photo' => $photo, 
			'path' => $path, 
			'page_title' => 'Photo of ' . $photo->alt_text,
			'next' => $next,
			'prev' => $prev,
			'first' => $first,
			'backLink' => '/galleries',
			'backLinkLabel' => 'Back to Gallery',
		]);		
		
		return view('photos.gallery', $vdata);
	}
	
    public function edit(Request $request, Photo $photo)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {
			$path = Controller::getPhotoPath($photo);
			$dates = null;
			if (isset($photo->display_date))
				$dates = Controller::getDateControlSelectedDate($photo->display_date);

			$vdata = $this->getViewData([
				'record' => $photo, 
				'path' => $path,
				'type' => Controller::getPhotoInfo($photo->type_flag)['type'],
				'dates' => Controller::getDateControlDates(),
				'filter' => $dates,
			]);		
			
			return view('photos.edit', $vdata);
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
			$id = intval($photo->parent_id);
			$info = Controller::getPhotoInfoPath($photo->type_flag, $photo->parent_id);
			$folder = $info['folder'];			
			$redirect = $info['redirect'];
			$path_from = $info['filepath'];
			
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
										
					// get and fix up the new file name
					$filename = $this->getPhotoName($filename, $request->filename_orig, $alt_text_default);
					
					// check for duplicate filesname and create a unique name if necessary
					$filenameUnique = Controller::getUniqueFilename($path_to, $filename);
					$duplicate = ($filenameUnique != $filename); // filename had to be changed to make it unique
					$filename = $filenameUnique;
					
					$path_from = Controller::appendPath($path_from, $request->filename_orig);
					$path_to = Controller::appendPath($path_to, $filename);
					
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
			$photo->permalink = str_replace(".jpg", "", $filename);
			$photo->alt_text = $alt_text;
			$photo->gallery_flag = isset($request->gallery_flag) ? 1 : 0;
			$photo->location = trim($request->location);
			$photo->display_date = Controller::getSelectedDate($request);
			session(['location' => $photo->location]); 

			// special fields for slider photos
			if (Controller::isSlider($photo->type_flag))
			{
				$photo->type_flag = PHOTO_TYPE_SLIDER; // default is both unset
				
				if (isset($request->horiz_flag) && isset($request->vert_flag))
					; // both checked, flag already set to all sliders
				else if (isset($request->horiz_flag))
					$photo->type_flag = PHOTO_TYPE_SLIDER_HORIZONTAL_ONLY;
				else if (isset($request->vert_flag))
					$photo->type_flag = PHOTO_TYPE_SLIDER_VERTICAL_ONLY;
			}
						
			// if photo is being set to main, unset any other main photo
			if (isset($request->main_flag) && !$photo->main_flag)
			{
				Photo::clearMainPhoto($photo->parent_id);
			}
			
			$photo->main_flag = isset($request->main_flag) ? 1 : 0;
			
			$photo->save();
				
			return redirect($redirect); 
		}
		else
		{
			return redirect('/');
		}
    }
    
    public function updateparent(Request $request, Photo $photo)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$parent_id_orig = $photo->parent_id;
		
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {
			$photo->parent_id = intval($request->parent_id);
			$photo->save();
		}

		return redirect('/photos/entries/' . $parent_id_orig);
	}	
	
    public function confirmdelete(Request $request, Photo $photo)
    {		
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {			
			$info = Controller::getPhotoInfoPath($photo->type_flag, $photo->parent_id);
			$path = $info['path'];
			
			$vdata = $this->getViewData([
				'photo' => $photo, 
				'path' => $path,
				'type' => $info['type'],
			]);
	
			return view('photos.confirmdelete', $vdata);
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

		$redirect = null;
		$message = null;
		$messageLevel = null;
		
		Controller::deletePhoto($photo, $redirect, $message, $messageLevel);
		
		$request->session()->flash('message.level', $messageLevel);
		$request->session()->flash('message.content', $message);
		
		return redirect($redirect);
    }

    public function rotate(Request $request, Photo $photo)
    {					
		$path = base_path() . '/public/img/' . PHOTO_ENTRY_FOLDER . '/' . $photo->parent_id . '/';

		//define image path
		$path = $path . $photo->filename;

		// Load the image
		$image = imagecreatefromjpeg($path);

		// Rotate
		$image = imagerotate($image, -90, 0);

		//and save it on your server...
		imagejpeg($image, $path);
		
		$redirect = '/photos/' . PHOTO_ENTRY_FOLDER . '/' . $photo->parent_id;

		return redirect($redirect);
	}
	
    public function permalink(Request $request, $permalink, $id)
    {	
		$id = intval($id);
		
		$photo = Photo::get($id);

		if (!isset($photo))
		{
			$msg = 'Page Not Found (404) for photo id: ' . $id;

			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $msg);
			
			$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$desc = $this->getVisitorInfoDebug();
			Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_VIEW, /* title = */ $msg, $desc, $id, null, null, $link);			
			
			$data['title'] = '404';
			$data['name'] = 'Page not found';
			return response()->view('errors.404', $data, 404);

			//return redirect('/');
		}
	
		// check parent type
		if (isset($photo->parent_id) && $photo->parent_id > 0)
		{
			$entry = Entry::select()
				->where('deleted_flag', 0)
				->where('id', $photo->parent_id)
				->first();
			
			if (isset($entry))
			{
				if ($entry->type_flag == ENTRY_TYPE_GALLERY)
				{
					$backLink = '/galleries/' . $entry->permalink;
					$backLinkLabel = 'Back to Gallery';
				}
				else
				{
					$backLink = '/entries/' . $entry->permalink;
					$backLinkLabel = 'Back to Entry';
					//dd($backLink);
				}
			}
			else
			{
				$msg = 'Invalid Photo Parent Found: ' . $photo->parent_id;

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
		
				Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_VIEW, /* title = */ $msg);			
		
				return redirect('/');
			}
		}
		else
		{
			$backLink = '/photos/sliders';
			$backLinkLabel = 'Back to List';
		}

		$this->saveVisitor(LOG_MODEL_PHOTOS, LOG_PAGE_GALLERY, $photo->id);

		$path = Controller::getPhotoPath($photo);
		$path = Controller::getPhotoPathRemote($path, $photo->site_id);
		
		$next = Photo::getNextPrev($photo->parent_id, $photo->id);
		$prev = Photo::getNextPrev($photo->parent_id, $photo->id, /* next = */ false);
		$first = Photo::getFirst($photo->parent_id);

		$vdata = $this->getViewData([
			'photo' => $photo, 
			'path' => $path, 
			'page_title' => 'Photo of ' . $photo->alt_text,
			'next' => $next,
			'prev' => $prev,
			'first' => $first,
			'backLink' => $backLink,
			'backLinkLabel' => $backLinkLabel,
			'bannerIndex' => mt_rand(1, BANNERS_FP_COUNT), // random banner index
		]);		
		
		return view('photos.gallery', $vdata);
	}
	
    public function slideshow(Request $request, Entry $entry)
    {		
		$next = null;
		$prev = null;
		$photos = [];
		if (isset($entry))
		{
			$gallery = isset($entry) ? $entry->photos : null;
			foreach($gallery as $photo)
			{
				$photos[] = $photo;
			}

			$p = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('parent_id', '=', $entry->id)
				->orderByRaw('id ASC')
				->get();
			foreach($p as $photo)
			{
				$photos[] = $photo;
			}
		}
		else
		{
			// log error			
			$msg = 'SlideShow Not Found for entry: ' . $entry->title;
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL_PHOTOS, LOG_ACTION_VIEW, /* title = */ $msg);			
			
            return redirect('/articles');
		}
		
		if (false)
		{
			$id = isset($entry) ? $entry->id : null;
			$this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
						
			if (isset($entry))
			{
				$entry->description = nl2br($entry->description);
				$entry->description = $this->formatLinks($entry->description);		
			}
			else
			{

			}
		}

		$backLink = null;
		$backLinkText = null;
			
		$vdata = $this->getViewData([
			'record' => $entry,
			'photo' => $photos[0], 
			'next' => $next,
			'prev' => $prev,
			'path' => PHOTO_ENTRY_PATH . $entry->id . '/',
		], 'Photo Slideshow');
		
		return view('photos.view', $vdata);
	}
	
	// attach photo to an entry as a many-to-many
    public function setmain(Photo $photo)
    { 
		if (!$this->isAdmin())
			return redirect('/');
	 		
		try 
		{	
			// clear current main photo
			Photo::clearMainPhoto($photo->parent_id);

			// set the photo as the main photo
			$photo->main_flag = true;
			$photo->gallery_flag = true; // has to be shown in the gallery too
			$photo->save();
		}
		catch (\Exception $e) 
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $e->getMessage());
		}
		
		return back();
	}
	
	public function setgallery(Photo $photo)
    { 
		if (!$this->isAdmin())
			return redirect('/');
 		
		try 
		{
			// flip the gallery flag
			$photo->gallery_flag = !$photo->gallery_flag;
			
			if ($photo->main_flag && !$photo->gallery_flag)
				$photo->main_flag = false;
				
			$photo->save();
		}
		catch (\Exception $e) 
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $e->getMessage());
		}
		
		return back();
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
}
