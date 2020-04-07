<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Http\Controllers\Controller;
use App\Entry;
use App\Photo;
use App\Event;
use DB;

define('PREFIX', 'galleries');
define('LOG_MODEL', 'galleries');
define('TITLE', 'Gallery');

class GalleryController extends Controller
{
    public function index()
    {				
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_GALLERY);
		
		$records = $this->getEntriesByType(ENTRY_TYPE_GALLERY);

		return view(PREFIX . '.index', $this->getViewData([
			'records' => $records, 
		], 'Photo Galleries'));
    }
	
    public function indexadmin(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
		
		try
		{
			$records = Entry::getEntriesByType(ENTRY_TYPE_GALLERY);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.indexadmin', $vdata);
    }

	public function view(Request $request, Entry $entry)
    {	
		$id = isset($entry) ? $entry->id : null;
		$this->saveVisitor(LOG_MODEL_GALLERIES, LOG_PAGE_VIEW, $id);
						
		if (isset($entry))
		{
			$this->countView($entry);
			$entry->description = nl2br($entry->description);
			$entry->description = $this->formatLinks($entry->description);		
		}
		else
		{
			$msg = 'Gallery Not Found';
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL_GALLERIES, LOG_ACTION_VIEW, /* title = */ $msg);			
			
            return redirect('/galleries');
		}
			
		$photos = Photo::select()
			->where('deleted_flag', 0)
			->where('gallery_flag', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
	
		$photo_path = '/img/entries/';
		$photo_path = count($photos) > 0 ? Controller::getPhotoPathRemote($photo_path, $photos[0]->site_id) : $photo_path;
		$photo_path .= $id;

		$fixed = [];
		foreach($photos as $record)
		{
			$this->makeThumbnailDirect($photo_path, $record->filename);
			$fixed[] = Photo::setPermalink($record);
		}
		$photos = $fixed;
		
		return view('galleries.view', $this->getViewData([
			'record' => $entry, 
			'photos' => $photos,
			'photo_path' => $photo_path,
		], 'Photo Gallery of ' . $entry->title));
    }
    
    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);

		$entry = Entry::select(DB::raw('entries.*, translations.*, entries.id as id, entries.approved_flag as approved_flag'))
			->leftJoin('translations', function ($join) {
				$join->on('entries.id', '=', 'translations.parent_id')
					 ->where('translations.language', '=', App::getLocale())
					 ->where('translations.parent_table', '=', 'entries');
			})
			->where('entries.deleted_flag', 0)
			->where('entries.permalink', $permalink)
			->first();
			
		$id = isset($entry) ? $entry->id : null;
		$this->saveVisitor(LOG_MODEL_GALLERIES, LOG_PAGE_PERMALINK, $id);
						
		if (isset($entry))
		{
			$this->countView($entry); // do this before record changed, otherwise changes will be saved
			
			// copy translations in
			if (isset($entry->medium_col1))
				$entry->title = $entry->medium_col1;

			$entry->description = nl2br($entry->description);
			$entry->description = $this->formatLinks($entry->description);		
		}
		else
		{
			$msg = 'Page Not Found (404) for gallery permalink: ' . $permalink;	
			Event::logError(LOG_MODEL_GALLERIES, LOG_ACTION_VIEW, /* title = */ $msg);			
			
			$data['title'] = '404';
			$data['name'] = 'Page not found';
			
			return response()->view('errors.404', $data, 404);
		}
			
		$photos = Photo::select()
			//->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('gallery_flag', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();

		$photo_path = '/img/entries/';
		$photo_path = count($photos) > 0 ? Controller::getPhotoPathRemote($photo_path, $photos[0]->site_id) : $photo_path;
		$photo_path .= $id;

		$fixed = [];
		foreach($photos as $record)
		{
			$this->makeThumbnailDirect($photo_path, $record->filename);
			$fixed[] = Photo::setPermalink($record);
		}
		$photos = $fixed;

		return view('galleries.view', $this->getViewData([
			'record' => $entry, 
			'photos' => $photos,
			'photo_path' => $photo_path,
		], 'Photo Gallery of ' . $entry->title));
	}
		
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}
		
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
			
		$record = new Entry();
		
		$record->site_id = SITE_ID;
		$record->user_id = Auth::id();
				
		$record->title					= $this->trimNull($request->title);
		$record->description			= $this->trimNull($request->description);

		$record->permalink = $this->trimNull($request->permalink);
		if (!isset($record->permalink))
			$record->permalink = $this->createPermalink($record->title);
		
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->title, $record->site_url, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->title, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
    }

	public function edit(Entry $entry)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$vdata = $this->getViewData([
			'record' => $entry,
		]);		
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Entry $entry)
    {
		$record = $entry;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		
		$record->title = $this->copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = $this->copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->permalink = $this->copyDirty($record->permalink, $request->permalink, $isDirty, $changes);
		
		// example of getting value from radio controls
		//$v = isset($request->radio_sample) ? intval($request->radio_sample) : 0;		
		//$record->radio_sample = $this->copyDirty($record->radio_sample, $v, $isDirty, $changes);		
		
		$v = isset($request->published_flag) ? 1 : 0;
		$record->published_flag = $v;
						
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->title, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'title = ' . $record->title, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}
		else
		{
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'No changes made to ' . $this->title);
		}

		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
	}
	
    public function confirmdelete(Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $entry,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Entry $entry)
    {	
		$record = $entry;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->title, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->title, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index');
    }	

    public function publish(Request $request, Entry $entry)
    {	
    	if (!$this->isOwnerOrAdmin($entry->user_id))
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $entry,
		]);
		
		return view(PREFIX . '.publish', $vdata);
    }
	
    public function publishupdate(Request $request, Entry $entry)
    {	
		$record = $entry; 
		
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($record->user_id))
        {			
			$published = isset($request->published_flag) ? 1 : 0;
			$record->published_flag = $published;
			
			if ($published === 0) // if it goes back to private, then it has to be approved again
				$record->approved_flag = 0;
			else
				$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			try
			{
				$record->save();
				Event::logEdit(LOG_MODEL, $record->title, $record->id, 'published/approved status updated');			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' status has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->title, null, $e->getMessage());

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
			
			//return redirect(route(PREFIX . '.permalink', [$record->permalink]));
			return redirect('/' . PREFIX . '/indexadmin');
		}
		else
		{
			return redirect('/');
		}
    }	

	// share photo with an entry
    public function share($entry_id)
    {		
		if (!$this->isAdmin())
			return redirect('/');
           
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('id', $entry_id)
			->first();
			
		$galleries = $this->getEntriesByType(
			ENTRY_TYPE_GALLERY
			, /* approved = */ false
			, /* limit = */ 10000
			, /* site_id = */ null
			, /* orderBy = */ ORDERBY_DATE
		);

		$vdata = $this->getViewData([
			'entry' => $entry,
			'galleries' => $galleries,
		]);
		
		return view('galleries.share', $vdata);      
	}
	
	// link photo with an entry
    public function link($entry_id, $gallery_id)
    {		
		if (!$this->isAdmin())
			return redirect('/');
           
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('id', $entry_id)
			->first();
			
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $gallery_id)
			->orderByRaw('created_at ASC')
			->get();
		
		return view('galleries.link', $this->getViewData([
			'entry' => $entry, 
			'photos' => $photos,
		]));		     
	}
	
	// attach photo to an entry as a many-to-many
    public function attach($entry_id, $photo_id)
    { 
		if (!$this->isAdmin())
			return redirect('/');

		$entry_id = intval($entry_id);
		$photo_id = intval($photo_id);
	 	$redirect = "/";
	 		
		try 
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('id', $entry_id)
				->first();
				
			if (isset($entry))
			{
				//throw new \Exception('entry not found');
			}
			
			// get the photo
			$photo = Photo::select()
					->where('id', abs($photo_id))
					->first();
					
			if (!isset($photo))
			{
				//throw new \Exception('photo not found');
			}
						
			if ($photo_id > 0)
			{
				//
				// add photo
				//
				$entry->photos()->save($photo);
	 			$redirect = "/galleries/link/$entry_id/$photo->parent_id";
			}
			else
			{
				//
				// remove photo
				//
				$entry->photos()->detach($photo);
				$redirect = "/photos/entries/$entry_id";
			}
		}
		catch (\Exception $e) 
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $e->getMessage());
		}
		
		return redirect($redirect);
	}

    public function attachasync($entry_id, $photo_id)
    { 
		if (!$this->isAdmin())
			return redirect('/');

		$entry_id = intval($entry_id);
		$photo_id = intval($photo_id);
	 		
		try 
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('id', $entry_id)
				->first();
				
			if (isset($entry))
			{
				//throw new \Exception('entry not found');
			}
			
			// get the photo
			$photo = Photo::select()
					->where('id', abs($photo_id))
					->first();
					
			if (!isset($photo))
			{
				//throw new \Exception('photo not found');
			}
						
			if ($photo_id > 0)
			{
				//
				// add photo
				//
				$entry->photos()->save($photo);
			}
			else
			{
				//
				// remove photo
				//
				$entry->photos()->detach($photo);
			}
		}
		catch (\Exception $e) 
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $e->getMessage());
		}
		
		return;
	}
	
    public function move(Request $request, Photo $photo)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$parent_id_orig = $photo->parent_id;
		$filename_orig = $photo->filename;
		$redirect = '/photos/entries/' . $parent_id_orig;
		
    	if ($this->isOwnerOrAdmin($photo->user_id))
        {
			//
			// move the physical photo to the folder of the new parent
			//
			$info_from = Controller::getPhotoInfoPath($photo->type_flag, $photo->parent_id);
			$info_to = Controller::getPhotoInfoPath($photo->type_flag, $request->parent_id);

			$path_from = $info_from['filepath'];
			$path_to = $info_to['filepath'];
			
			// check for unique filename in the destination folder
			$filename = Controller::getUniqueFilename($path_to, $photo->filename);
			$duplicate = ($filename != $photo->filename); // filename had to be changed to make it unique
			
			$path_from = Controller::appendPath($path_from, $photo->filename);
			$folder_to = $path_to; // save the folder in case we have to create it
			$path_to = Controller::appendPath($path_to, $filename);
			
			try
			{				
				// Stop 0: make sure the destination folder exists
				if (!is_dir($folder_to))
				{
					// make the folder with read/execute for everybody
					mkdir($folder_to, 0755);					
				}
				
				// Step 1: move the file
				rename($path_from, $path_to);
			
				// Step 2: change the photo's parent to the gallery id
				$photo->parent_id = intval($request->parent_id);
				$photo->filename = $filename;
				$photo->main_flag = 0;
				$photo->save();
				
				// Step 3: link the photo back to the entry that we removed it from
				if (isset($request->entry_id))
				{
					$this->attach($request->entry_id, $photo->id);
				}
				
				$msg = 'Photo ' . $filename_orig . ' moved to Gallery ' . $parent_id_orig;
				Event::logInfo(LOG_MODEL, LOG_ACTION_MOVE, $msg);
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $msg);
			}
			catch (\Exception $e) 
			{
				$msg = $path_from . ' to ' . $path_to;
				Event::logException(LOG_MODEL, LOG_ACTION_MOVE, $msg, null, $e->getMessage());

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
				return redirect($redirect);
			}
			
			
		}

		return redirect($redirect);
	}

	// attach photo to an entry as a many-to-many
    public function setmain($entry_id, $photo_id)
    { 
		if (!$this->isAdmin())
			return redirect('/');

		$entry_id = intval($entry_id);
		$photo_id = intval($photo_id);
	 		
		try 
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('id', $entry_id)
				->first();
				
			if (isset($entry))
			{
				//throw new \Exception('entry not found');
			}
			
			// get the photo
			$photo = Photo::select()
					->where('id', abs($photo_id))
					->first();
					
			if (!isset($photo))
			{
				//throw new \Exception('photo not found');
			}
			
			//
			// set the photo as the main photo
			//
			$entry->photo_id = $photo->id;
			$entry->save();
		}
		catch (\Exception $e) 
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $e->getMessage());
		}
		
		return redirect("/photos/entries/$entry->id");
	}
}
