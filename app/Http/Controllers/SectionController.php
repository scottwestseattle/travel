<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Entry;
use App\Event;
use App\Photo;

define('PREFIX', 'sections');
define('LOG_MODEL', 'sections');
define('TITLE', 'Sections');

class SectionController extends Controller
{
    public function index()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('site_id', $this->getSiteId())
			//->where('site_id', SITE_ID)
			->where('type_flag', ENTRY_TYPE_SECTION)
			->where('deleted_flag', 0)
			->orderByRaw('entries.id DESC')
			->get();
			
		$vdata = $this->getViewData([
			'records' => $entries,
		]);
			
    	return view('sections.index', $vdata);
    }
	
    public function add(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'entryTypes' => $this->getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request),
			'type_flag' => ENTRY_TYPE_SECTION,
			'site_id' => $this->getSiteId(),
			'referer' => '/sections',
		]);
		
		return view('sections.add', $vdata);
	}

    public function view($id)
    {
		$id = intval($id);
		
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW, $id);
	
		$entry = Entry::select()
			->where('deleted_flag', '<>', 1)
			->where('id', $id)
			->first();
		
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
		
		$vdata = $this->getViewData([
			'record' => $entry, 
			'photos' => $photos,
		]);
		
		return view('entries.view', $vdata);
	}

    public function show(Request $request, $id)
    {		
		$id = intval($id);
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_SHOW, $id);
		
		$next = null;
		$prev = null;
		$photos = null;
		
		try 
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('id', $id)
				->first();
								
			if (isset($entry))
			{
				$entry->description = nl2br($entry->description);
				$entry->description = $this->formatLinks($entry->description);		
			}
				
			if (!isset($entry))
			{
				$msg = 'Record not found, ID: ' . $entry->id;
				Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
						
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
			}
			else if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
			{						
				if (isset($entry->display_date))
				{
					$next = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id);
					$prev = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id, /* next = */ false);
				}
				else
				{
					$msg = 'Missing Display Date to show record: ' . $entry->id;
					Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
						
					$request->session()->flash('message.level', 'danger');
					$request->session()->flash('message.content', $msg);
				}
				
				$photos = Photo::select()
					//->where('site_id', SITE_ID)
					->where('deleted_flag', 0)
					->where('parent_id', $entry->id)
					->orderByRaw('id ASC')
					->get();
			}
							
			$vdata = $this->getViewData([
				'record' => $entry, 
				'next' => $next,
				'prev' => $prev,
				'photos' => $photos,
			]);
			
			return view('entries.view', $vdata);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_SELECT, $this->getTextOrShowEmpty(isset($entry) ? $entry->title : 'record not found'), null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}			
		
        return redirect('/error');
	}
	
    public function edit(Request $request, Entry $entry)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$dates = null;
		if (isset($entry->display_date))
			$dates = Controller::getDateControlSelectedDate($entry->display_date);
		
		$vdata = $this->getViewData([
			'record' => $entry,
			'entryTypes' => Controller::getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => $dates,
			'referer' => '/sections',
		]);
		
		return view('sections.edit', $vdata);
    }

    public function publish(Request $request, Entry $entry)
    {	
    	if (!$this->isOwnerOrAdmin($entry->user_id))
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $entry,
			'referer' => '/sections',
		]);
		
		return view('entries.publish', $vdata);
    }
	
    public function confirmdelete(Request $request, Entry $entry)
    {		
    	if (!$this->isOwnerOrAdmin($entry->user_id))
             return redirect('/');
		
		$entry->description = nl2br(trim($entry->description));
		
		return view('entries.confirmdelete', $this->getViewData([
				'entry' => $entry,
				'referer' => '/sections',
			])
		);
    }
}
