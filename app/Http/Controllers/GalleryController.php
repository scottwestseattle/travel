<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entry;
use App\Photo;
use App\Event;

define('PREFIX', 'galleries');
define('LOG_MODEL', 'galleries');
define('TITLE', 'Gallery');

class GalleryController extends Controller
{
	public function __construct ()
	{
		$this->prefix = PREFIX;
		$this->title = TITLE;
	}
	
    public function index()
    {				
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_GALLERY);
		
		$records = Entry::getEntriesByType(ENTRY_TYPE_GALLERY);
		
		return view(PREFIX . '.index', $this->getViewData([
			'records' => $records, 
		]));
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

    public function permalink(Request $request, $permalink)
    {
		$next = null;
		$prev = null;
		
		$permalink = trim($permalink);
		
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('permalink', $permalink)
			->first();
			
		$id = isset($entry) ? $entry->id : null;
		$this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
						
		if (isset($entry))
		{
			$entry->description = nl2br($entry->description);
			$entry->description = $this->formatLinks($entry->description);		
		}
		else
		{
			$msg = 'Permalink Entry Not Found: ' . $permalink;
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
			
            return redirect('/entries/index');
		}
		
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			if (isset($entry->display_date))
			{
				$next = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id);
				$prev = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id, /* next = */ false);
			}
			else
			{
				$msg = 'Missing Display Date to view record: ' . $entry->id;
				Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
						
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
			}
		}
			
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
			
		$vdata = $this->getViewData([
			'record' => $entry, 
			'next' => $next,
			'prev' => $prev,
			'photos' => $photos,
		]);
		
		return view('galleries.view', $vdata);
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
	
	public function view(Entry $entry)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$vdata = $this->getViewData([
			'record' => $entry,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
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

}
