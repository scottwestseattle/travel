<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App;
use App\Entry;
use App\Event;
use App\Photo;
use App\Location;
use App\Tools;
use App\Translation;
use App\Comment;
use Cookie;

define('BODYSTYLE', '<span style="color:green;">');
define('ENDBODYSTYLE', '</span>');
define('EMPTYBODY', 'Empty Body');
define('BODY', 'Body');
define('INTNOTSET', -1);

define('PREFIX', 'entries');
define('LOG_MODEL', 'entries');
define('TITLE', 'Entries');


class EntryController extends Controller
{
    public function index()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('site_id', SITE_ID)
			//->where('type_flag', '<>', ENTRY_TYPE_TOUR)
			->where('deleted_flag', 0)
			->orderByRaw('entries.id DESC')
			->get();
			
		$vdata = $this->getViewData([
			'records' => $entries,
		]);
			
    	return view('entries.index', $vdata);
    }
	
    public function articles()
    {		
		$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

		$records = $this->getEntriesByType(ENTRY_TYPE_ARTICLE, /* approved = */ false); // get all because they are displayed by super admin
			
		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'List of Articles',
		]);
			
    	return view('entries.articles', $vdata);
    }

    public function hotels()
    {		
		$this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_INDEX);

		$records = $this->getEntriesByType(ENTRY_TYPE_HOTEL, /* approved = */ false); // get all because they are displayed by super admin
			
		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'Hotels',
		]);
			
    	return view('entries.hotels', $vdata);
    }
    
    public function indexadmin($type_flag = null)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = $this->getEntriesByType($type_flag, false, 0, null, false, ORDERBY_DATE);

		$vdata = $this->getViewData([
			'records' => $entries,
			'redirect' => '/entries/indexadmin',
			'entryTypes' => Controller::getEntryTypes(),
		]);
		
    	return view('entries.indexadmin', $vdata);
    }
	
    public function tag($tag_id)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('site_id', SITE_ID)
			->where('user_id', '=', Auth::id())
			//->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();

		$vdata = $this->getViewData([
			'entries' => $entries,
		]);
			
    	return view('entries.index', $vdata);
    }

    public function posts()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('site_id', SITE_ID)
			->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		$vdata = $this->getViewData([
			'entries' => $entries, 
			'data' => $this->getViewData(), 
			'title' => 'Posts',
		]);
		
		return view('entries.index', $vdata);
    }
	
    public function tours()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$entries = Entry::select()
			->where('site_id', '=', SITE_ID)
			->where('user_id', '=', Auth::id())
			->where('type_flag', '=', ENTRY_TYPE_TOUR)
			->orderByRaw('entries.id DESC')
			->get();
		
		$vdata = $this->getViewData([
			'records' => $entries,
		]);
		
		return view('entries.index', $vdata);
    }
	
    public function add(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$locations = Location::getPlaces();
				
		$vdata = $this->getViewData([
			'entryTypes' => $this->getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request),
			'locations' => $locations,
		]);
		
		return view('entries.add', $vdata);
	}
	
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
        			
		$entry = new Entry();
		
		if (true) // new way
			$entry->site_id = isset($request->site_id) ? $request->site_id : SITE_ID;
		else // old way
			$entry->site_id = SITE_ID;
		
		$entry->user_id = Auth::id();
		$entry->type_flag = $request->type_flag;

		$entry->parent_id 			= $request->parent_id;		
		$entry->title 				= $this->trimNull($request->title);
		$entry->description_short	= $this->trimNull($request->description_short);
		$entry->description			= $this->trimNull($request->description);
		$entry->display_date 		= Controller::getSelectedDate($request);

		$entry->permalink			= $this->trimNull($request->permalink);
		if (!isset($entry->permalink))
			$entry->permalink = $this->createPermalink($entry->title, $entry->display_date);
		
		// for blog entries, save the location
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			if (!empty($entry->description_short))
			{
				Cookie::queue('blogEntryLocation', $entry->description_short);
			}
			
			// fix text
			$entry->description = Tools::fixLinePunct($entry->description);
		}

		try
		{
			$entry->save();

			$msg = 'Entry has been added';
			Event::logAdd(LOG_MODEL_ENTRIES, $msg . ': ' . $entry->title, $entry->description, $entry->id);
				
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $msg);

			$redirect = $this->getReferer($request, '/entries/show/' . $entry->id);
			
			return redirect($this->getReferer($request, '/entries/show/' . $entry->id)); 
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_ADD, $this->getTextOrShowEmpty($entry->title), null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		

			return redirect('/sections'); 
			return redirect($this->getReferer($request, '/entries/indexadmin/'));
		}						
    }
	
    public function permalink(Request $request, $permalink)
    {		
		$next = null;
		$prev = null;
		
		// get the entry the Laravel way so we can access the gallery photo list
		$entry = null;
		if ($this->isAdmin())
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('permalink', $permalink)
				->first();
		}
		else
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('deleted_flag', 0)
				->where('permalink', $permalink)
				->first();
		}
			
		$isRobot = false;
		$gallery = null;
		if (isset($entry))
		{
			$this->countView($entry);
			$gallery = $entry->photos;
		
			// get the entry the mysql way so we can have all the main photo and location info
			//$entry = Entry::getEntry($permalink);
			$entry = Entry::get($permalink); // new way with translation included

			$id = isset($entry) ? $entry->id : null;
			$visitor = $this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
			$isRobot = isset($visitor) && $visitor->robot_flag;
		}
						
		if (isset($entry))
		{
			$entry->description = nl2br($entry->description);
			$entry->description = $this->formatLinks($entry->description);		
		}
		else
		{        
			$msg = 'Page Not Found (404) for permalink: ' . $permalink;
			
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', $msg);
			
			$desc = $this->getVisitorInfoDebug();
			Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg, $desc);			
		
			$data['title'] = '404';
			$data['name'] = 'Page not found';
			return response()->view('errors.404', $data, 404);
		}
		
		$page_title = $entry->title;
		$backLink = null;
		$backLinkText = null;
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			$page_title = 'Blog Post - ' . $page_title;
			
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
		else if ($entry->type_flag == ENTRY_TYPE_ARTICLE)
		{
			$backLink = '/articles';
			$backLinkText = __('content.Back to Article List');
			$page_title = __('ui.Article') . ' - ' . $page_title;
			
			$next = Entry::getNextPrevEntry($entry);
			$prev = Entry::getNextPrevEntry($entry, /* next = */ false);
		}
		else if ($entry->type_flag == ENTRY_TYPE_HOTEL)
		{
			$backLink = '/hotels';
			$backLinkText = __('content.Back to Hotel List');
			$page_title = __('content.Hotels') . ' - ' . $page_title;
			
			$next = Entry::getNextPrevEntry($entry);
			$prev = Entry::getNextPrevEntry($entry, /* next = */ false);
		}
		else if ($entry->type_flag == ENTRY_TYPE_LESSON)
		{
			$backLink = '/lessons';
			$backLinkText = __('content.Back to Lessons');
			$page_title = __('ui.Lesson') . ' - ' . $page_title;
		}		
		
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
			
		$comments = Comment::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('approved_flag', 1)
			->where('parent_id', $entry->id)
			->orderByDesc('id')
			->get();

		$vdata = $this->getViewData([
			'record' => $entry, 
			'next' => $next,
			'prev' => $prev,
			'photos' => $photos,
			'gallery' => $gallery,
			'backLink' => $backLink,
			'backLinkText' => $backLinkText,
			'page_title' => $page_title,
			'display_date' => Controller::translateDate($entry->display_date),
			'comments' => $comments,
			'isRobot' => false, // $isRobot, //todo: not yet because ome spam robot is coming from fikirandroy page (don't want them to switch pages)
		]);
		
		return view('entries.view', $vdata);
	}
	
    public function view($title, $id)
    {
		$id = intval($id);
		
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW, $id);
	
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('id', $id)
			->first();
		
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
		
		$comments = Comment::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('parent_id', $entry->id)
			->get();
			
		$vdata = $this->getViewData([
			'record' => $entry, 
			'photos' => $photos,
			'comments' => $comments,
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
	
    public function home()
    {
		if (!$this->isAdmin())
             return redirect('/');

		return $this->index();
	}
	
    public function edit(Request $request, Entry $entry)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$dates = null;
		if (isset($entry->display_date))
			$dates = Controller::getDateControlSelectedDate($entry->display_date);
		
		$translations = Translation::select()
			->where('parent_id', $entry->id)
			->where('parent_table', 'entries')
			->get();

		$languages = [];
		$languages[] = 'es';
		$languages[] = 'zh';

		$location = isset($entry->description_short) ? $entry->description_short : Cookie::get('blogEntryLocation');

		$vdata = $this->getViewData([
			'record' => $entry,
			'entryTypes' => Controller::getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => $dates,
			'translations' => $translations,
			'languages' => $languages,
			'location' => $location,
		]);
		
		return view('entries.edit', $vdata);
    }
	
    public function update(Request $request, Entry $entry)
    {
		$record = $entry;

		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {	
			if ($record->type_flag == ENTRY_TYPE_BLOG_ENTRY && $record->type_flag != $request->type_flag)
				$record->parent_id = null; // changing from blog entry to something else, remove the parent id 
			
			$record->type_flag 			= $request->type_flag;
			
			$prevTitle = $record->title;
			$record->title 				= $this->trimNull($request->title);
			$record->permalink			= $this->trimNull($request->permalink);
			$record->description_short	= $this->trimNull($request->description_short);
			$record->description		= $this->trimNull($request->description);
			$record->display_date 		= Controller::getSelectedDate($request);
			
			// for blog entries, save the location
			if ($record->type_flag == ENTRY_TYPE_BLOG_ENTRY)
			{
				Cookie::queue('blogEntryLocation', $record->description_short);
				
				// fix text
				$record->description = Tools::fixLinePunct($record->description);
			}
			
			//todo: finish the colors
			if (false && isset($request->color_foreground) && isset($request->colors))
				$record->color_foreground = EntryController::getColorCode($request->color_foreground, $request->colors);
			if (false && isset($request->color_background) && isset($request->colors))
				$record->color_background = EntryController::getColorCode($request->color_background, $request->colors);
				
			//$record->color_background = $request->color_background;
			
			//todo: turned off for now: $record->approved_flag = 0;
			
			//
			// write translation records
			//
			if (isset($request->translations))
			{
				foreach($request->translations as $key => $value)
				{
					$rc = Translation::updateEntry(
						$entry->id
						, 'entries'
						, $value						// language
						, $request->medium_col1[$key]	// title
						, $request->permalink			// permalink
						, $request->large_col1[$key]	// description
						, $request->large_col2[$key]	// description_short
					);
				
					if ($rc['saved'])
					{
						Event::logEdit(LOG_MODEL_TRANSLATIONS, $entry->title, $entry->id);			
					
						$request->session()->flash('message.level', 'success');
						$request->session()->flash('message.content', $rc['logMessage']);
					}
					else
					{
						Event::logException(LOG_MODEL_TRANSLATIONS, $rc['logAction'], $this->getTextOrShowEmpty($entry->title), null, $rc['exception']);
					
						$request->session()->flash('message.level', 'danger');
						$request->session()->flash('message.content', $rc['exception']);
					}			
				}
			}			
			
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL_ENTRIES, $record->title, $record->id, $prevTitle . '  ' . $record->title);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Entry has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_EDIT, $this->getTextOrShowEmpty($record->title), null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}			

			return redirect($this->getReferer($request, '/entries/indexadmin')); 
		}
		else
		{
			return redirect('/');
		}
    }

    public function getColorCode($index, $colors)
    {
		if ($index > 0)
		{
			return $colors[$index];
		}
		else
		{
			return null;
		}
	}	
	
    public function confirmdelete(Request $request, Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($entry->user_id))
        {
			$entry->description = nl2br(trim($entry->description));
			
			if ($entry->type_flag === ENTRY_TYPE_TOUR)
			{
				$vdata = $this->getViewData([
					'record' => $entry,
				]);

				return view('tours.confirmdelete', $vdata);
			}
			else
			{
				$vdata = $this->getViewData([
					'entry' => $entry,
				]);
				
				return view('entries.confirmdelete', $vdata);
			}
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {			
			$entry->deleteSafe();
			
			return redirect($this->getReferer($request, '/entries/index')); 
		}
		
		return redirect('/');
    }
	
    public function viewcount(Entry $entry)
    {		
		$this->countView($entry);
		
    	return view('entries.viewcount');
	}

    public function publish(Request $request, Entry $entry)
    {	
    	if (!$this->isOwnerOrAdmin($entry->user_id))
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $entry,
		]);
		
		return view('entries.publish', $vdata);
    }
	
    public function publishupdate(Request $request, Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {			
			$entry->published_flag = isset($request->published_flag) ? 1 : 0;
			$entry->approved_flag = isset($request->approved_flag) ? 1 : 0;
			$entry->finished_flag = isset($request->finished_flag) ? 1 : 0;
			$entry->parent_id = $request->parent_id;
			$entry->view_count = intval($request->view_count);

			$entry->save();
			
			return redirect($this->getReferer($request, '/entries/' . $entry->permalink)); 
		}
		else
		{
			return redirect('/');
		}
    }

    public function setlocation(Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		 
		$locations = Location::select()
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->orWhere('location_type', LOCATION_TYPE_COUNTRY)
			->orderByRaw('locations.name ASC')
			->get();

		$current_location = $entry->locations()->orderByRaw('location_type DESC')->first();
			
    	if ($this->isOwnerOrAdmin($entry->user_id))
        {			
			$vdata = $this->getViewData([
				'record' => $entry, 
				'locations' => $locations, 
				'current_location' => $current_location,
			]);
	
			return view('entries.location', $vdata);
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function locationupdate(Request $request, Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {									
			$entry_id = $entry->id;
			$location_id = intval($request->location_id);

			if ($location_id <= 0)
			{
				//
				// location is being removed
				//
				
				// remove the hasMany
				$entry->locations()->detach();
				
				// remove the hasOne
				$entry->location_id = null;
				$entry->save();
			}
			else
			{
				// get the new location
				$location = Location::select()
					->where('id', $location_id)
					->first();

				if (isset($location))
				{
					//
					// remove all current locations so they can be replaced
					//
					$entry->locations()->detach();
					
					//
					// set the primary has-one location
					//
					$entry->location_id = $location_id;
					$entry->save();
					
					//
					// set the hasmany locations
					//
					
					$locations = DB::table('entries')
						->leftJoin('locations as l1', 'entries.location_id', '=', 'l1.id')
						->leftJoin('locations as l2', 'l1.parent_id', '=', 'l2.id')
						->leftJoin('locations as l3', 'l2.parent_id', '=', 'l3.id')
						->leftJoin('locations as l4', 'l3.parent_id', '=', 'l4.id')
						->leftJoin('locations as l5', 'l4.parent_id', '=', 'l5.id')
						->leftJoin('locations as l6', 'l5.parent_id', '=', 'l6.id')
						->leftJoin('locations as l7', 'l6.parent_id', '=', 'l7.id')
						->leftJoin('locations as l8', 'l7.parent_id', '=', 'l8.id')
						->select(
							  'l1.name as loc1', 'l1.id as loc1_id'
							, 'l2.name as loc2', 'l2.id as loc2_id'
							, 'l3.name as loc3', 'l3.id as loc3_id'
							, 'l4.name as loc4', 'l4.id as loc4_id'
							, 'l5.name as loc5', 'l5.id as loc5_id'
							, 'l6.name as loc6', 'l6.id as loc6_id'
							, 'l7.name as loc7', 'l7.id as loc7_id'
							, 'l8.name as loc8', 'l8.id as loc8_id'
						)
						->where('entries.id', $entry->id)
						->first();
					
					try 
					{
						$this->saveLocations($entry, $locations);
					}
					catch (\Exception $e) 
					{
						$request->session()->flash('message.level', 'danger');
						$request->session()->flash('message.content', $e->getMessage());
					}									
				}
			}
			
			return redirect(route('entry.permalink', [$entry->permalink]));
		}
		else
		{
			return redirect('/');
		}
    }	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
	
    private function fixEmpty(string $text, string $show)
    {	
		if (mb_strlen($text) === 0)
		{
			$text = BODYSTYLE 
			. '(' . strtoupper(__($show)) . ')'
			. ENDBODYSTYLE;			

		}
		
		return $text;
	}
	
	private function merge($layout, $text, $style = false)
	{
		$body = trim($text);
		if (mb_strlen($body) == 0)
		{
			if ($style === true)
			{
				// only show empty in the view version
				$body = '(' . strtoupper(__(EMPTYBODY)) . ')';
			}
			else
			{
				// leave a space for the copy version
				$body = ' ';
			}
		}
		
		if (mb_strlen($layout) > 0)
		{
			if ($style)
				$body = BODYSTYLE . $body . ENDBODYSTYLE;
				
			$text = nl2br(str_replace(BODY_PLACEHODER, $body, trim($layout))) . '<br/>';
		}
		else
		{
			$text = nl2br($body) . '<br/>';
		}
	
		return $text;
	}		

    public function gallery()
    {		   
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_GALLERY);
		
		$records = $this->getEntriesByType(ENTRY_TYPE_GALLERY);

		$vdata = $this->getViewData([
			'records' => $records, 
		]);
		
		return view('entries.gallery', $vdata);
    }
}
