<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Entry;
use App\Event;
use App\Photo;
use App\Location;

define('BODYSTYLE', '<span style="color:green;">');
define('ENDBODYSTYLE', '</span>');
define('EMPTYBODY', 'Empty Body');
define('BODY', 'Body');
define('INTNOTSET', -1);

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
		$records = Entry::getEntriesByType(ENTRY_TYPE_ARTICLE);
			
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
    	return view('entries.articles', $vdata);
    }
	

    public function indexadmin($type_flag = null)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::getEntriesByType($type_flag, /* approved = */ false);

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
			
		//dd($entries);
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

		$vdata = $this->getViewData([
			'entryTypes' => $this->getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request),
		]);
		
		return view('entries.add', $vdata);
	}
	
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           
			//dd($request);
			
		$entry = new Entry();
		
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
		
		try
		{
			$entry->save();

			$msg = 'Entry has been added';
			Event::logAdd(LOG_MODEL_ENTRIES, $msg . ': ' . $entry->title, $entry->description, $entry->id);
				
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $msg);

			return redirect($this->getReferer($request, '/entries/show/' . $entry->id)); 
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_ADD, $this->getTextOrShowEmpty($entry->title), null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		

			return redirect($this->getReferer($request, '/entries/indexadmin/'));
		}						
    }

    public function permalocation($location, $permalink)
    {
		dd('function permalocation: ' . $permalink);
		return $this->permalink($permalink);
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
		
		return view('entries.view', $vdata);
	}
	
    public function view($title, $id)
    {
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
		
		$vdata = $this->getViewData([
			'record' => $entry, 
			'photos' => $photos,
		]);
		
		return view('entries.view', $vdata);
	}

    public function show(Request $request, $id)
    {		
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
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_SELECT, $this->getTextOrShowEmpty(isset($entry) ? $entry->title : 'record not found'), null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}			
		
		return view('entries.view', $vdata);
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
		
		//dd($entry->display_date);
		$dates = null;
		if (isset($entry->display_date))
			$dates = Controller::getDateControlSelectedDate($entry->display_date);
		
		$vdata = $this->getViewData([
			'record' => $entry,
			'entryTypes' => Controller::getEntryTypes(),
			'dates' => Controller::getDateControlDates(),
			'filter' => $dates,
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
			
			$record->title 				= $this->trimNull($request->title);
			$record->permalink			= $this->trimNull($request->permalink);
			$record->description_short	= $this->trimNull($request->description_short);
			$record->description		= $this->trimNull($request->description);
			$record->display_date 		= Controller::getSelectedDate($request);
			
			//todo: turned off for now: $record->approved_flag = 0;
			
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL_ENTRIES, $record->title, $record->id);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Entry has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL_ENTRIES, LOG_ACTION_EDIT, $this->getTextOrShowEmpty($record->title), null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}			

			//dd($request->referer);
			return redirect($this->getReferer($request, '/entries/indexadmin')); 
		}
		else
		{
			return redirect('/');
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
			//dd($entry);
			
			$entry->deleteSafe();
			
			return redirect('/entries/index');
		}
		
		return redirect('/');
    }
	
    public function viewcount(Entry $entry)
    {		
    	$entry->view_count++;
    	$entry->save();	
    	return view('entries.viewcount');
	}

    public function hash()
    {		
		$data['hash'] = '';
		$data['hashed'] = '';
		
    	return view('entries.hash', $data);
	}
	
	public function hasher(Request $request)
	{
		$hash = trim($request->get('hash'));
		$hashed = $this->getHash($hash);

		$data['hash'] = $hash;
		$data['hashed'] = $hashed;

		return view('entries.hash', $data);
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
			$published = isset($request->published_flag) ? 1 : 0;
			$entry->published_flag = $published;
			
			if ($published === 0) // if it goes back to private, then it has to be approved again
				$entry->approved_flag = 0;
			else
				$entry->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$entry->parent_id = $request->parent_id;
			$entry->view_count = intval($request->view_count);
			
			$entry->save();
			
			return redirect(route('entry.permalink', [$entry->permalink]));
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
			->orderByRaw('locations.location_type ASC')
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
					//dd($locations);
					
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

    private function getHash($text) 
	{
		$s = sha1(trim($text));
		$s = str_ireplace('-', '', $s);
		$s = strtolower($s);
		$s = substr($s, 0, 8);
		$final = '';

		for ($i = 0; $i < 6; $i++)
		{
			$c = substr($s, $i, 1);
				
			if ($i % 2 != 0)
			{
				if (ctype_digit($c))
				{
                    if ($i == 1)
                    {
                        $final .= "Q";
                    }
                    else if ($i == 3)
                    {
                        $final .= "Z";
                    }
                    else
                    {
                        $final .= $c;
                    }
				}
				else
				{
					$final .= strtoupper($c);
				}
			}
			else
			{
				$final .= $c;
			}
		}

		// add last 2 chars
		$final .= substr($s, 6, 2);
		
		//echo $final;
		
		return $final;
	}			
}
