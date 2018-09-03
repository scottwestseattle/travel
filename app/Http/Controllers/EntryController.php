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

		$records = $this->getEntriesByType(ENTRY_TYPE_ARTICLE, false, 0, true);
			
		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'List of Articles',
		]);
			
    	return view('entries.articles', $vdata);
    }

    public function indexadmin($type_flag = null)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = $this->getEntriesByType($type_flag, /* approved = */ false);

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
	
    public function permalink(Request $request, $permalink)
    {		
		$next = null;
		$prev = null;
		$permalink = trim($permalink);
		
		// get the entry the Laravel way so we can access the gallery photo list
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('permalink', $permalink)
			->first();
		$gallery = isset($entry) ? $entry->photos : null;
		
		// get the entry the mysql way so we can have all the main photo and location info
		$entry = Entry::getEntry($permalink);
			
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
			$backLinkText = 'Back to Article List';
			$page_title = 'Article - ' . $page_title;
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
			'gallery' => $gallery,
			'backLink' => $backLink,
			'backLinkText' => $backLinkText,
			'page_title' => $page_title,
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

    public function gallery()
    {		   
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_GALLERY);
		
		$records = $this->getEntriesByType(ENTRY_TYPE_GALLERY);

		$vdata = $this->getViewData([
			'records' => $records, 
		]);
		
		return view('entries.gallery', $vdata);
    }

	private $tests = [
		['EXPECTED NOT FOUND', '/', ''],
		['Affiliates', '/', ''],
		['Buddha', '/', ''],
		['Exploring', '/', ''],
		['Tours, Hikes, Things To Do', '/', ''],
		['USA', '/', ''],
		['Show All Articles', '/', ''],
		['Show All Blogs', '/', ''],
		['Login', '/login', ''],
		['Register', '/register', ''],
		['Reset Password', '/password/reset', ''],
		['About', '/about', ''],
		['Todos Derechos Reservados', '/gallery', ''],
		['All Rights Reserved', '/galleries', ''],
		['Featured Photos', '/photos/sliders', ''],
		['Siem Reap', '/photos/view/64', ''],
		['Epic Euro Trip', '/blogs/index', ''],
		['Show All Posts', '/blogs/show/105', ''],
		['Day 71', '/blogs/show/31', ''],
		['Beijing Summer Palace', '/entries/show/157', ''], // prev
		['Thursday, Tienanmen Square', '/entries/show/155', ''], // next
		['Big Asia', '/blogs/show/105', ''], // back to blog
		['Seattle Waterfront to Lake Union', '/tours/index', ''],
		['Seattle', '/tours/location/2', ''],
		['China', '/tours/location/9', ''],
		['Articles', '/articles', ''],
		];
		
    public function test(Request $request)
    {	
		$executed = null;
		
		//$server = 'http://epictravelguide.com';
		//$server = 'http://localhost';
		//$server = 'http://grittytravel.com';
		//$server = 'http://hikebikeboat.com';
		$server = 'http://scotthub.com';

		$tests = array_merge($this->tests, EntryController::getTestEntries());

		if (isset($request->test_server))
		{
			$executed = true;
			
			for ($i = 0; $i < count($tests); $i++)
			{			
				// if item is checked
				if (isset($request->{'test'.$i}))
				{
					$tests[$i][2] = $this->testPage($request->test_server . $tests[$i][1], $tests[$i][0])['results'];
				}
			}
		}

		return view('entries.test', $this->getViewData([
			'records' => $tests,
			'test_server' => $server,
			'executed' => $executed,
		]));
	}
	
	static protected function getTestEntries()
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND type_flag in (2,3,4,5,8)
				AND deleted_flag = 0
				AND published_flag = 1 
				AND approved_flag = 1
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);
			
		//dd($records);
		
		$tests = [];
		
		foreach($records as $record)
		{
			$entryUrls = Controller::getEntryUrls();
			$type = $entryUrls[$record->type_flag];

			if (isset($type))
			{
				if ($record->type_flag == ENTRY_TYPE_BLOG) // blogs don't use permalink
				{
					$tests[] = [substr($record->title, 0, 10), '/' . $type . '/view/' . $record->id, ''];	
				}
				else // everything else uses permalinks
				{
					$tests[] = [substr($record->title, 0, 10), '/' . $type . '/' . $record->permalink, ''];
				}
			}
		}
		//dd($tests);
		
		return $tests;
	}
	
    public function testresults(Request $request)
    {
		$results = [];
				
		for ($i = 0; $i < count($this->tests); $i++)
		{			
			// if item is checked
			if (isset($request->{'test'.$i}))
			{
				$results[] = $this->testPage($request->test_server . $this->tests[$i][1], $this->tests[$i][0]);
			}
		}
		
		return view('entries.test', $this->getViewData([
			'records' => $results,
			'test_server' => $server,
		]));
    }
	
    public function testPage($url, $expected)
    {
		$text = '';
		$results['url'] = $url;
		$results['expected'] = $expected;

		try
		{
			$text = $this->file_get_contents_curl($url);
			//dd($url . ': ' . $text);
			$results['results'] = strpos($text, $expected) === false ? 'EXPECTED NOT FOUND' : 'success';
		}
		catch (\Exception $e) 
		{
			//$error = $e->getMessage();
			$results['results'] = 'ERROR OPENING PAGE: ' . $url;
		}	
				
		return $results;
	}
	
	private function file_get_contents_curl($url) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}

    public function search(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$search = null;
		$records = null;
		
		//dd($request);
		
		if (isset($request->searchText))
		{
			$search = trim($request->searchText);
			
			if (strlen($search) > 1)
			{
				try
				{
					$records = Controller::searchEntries($search);

				}
				catch (\Exception $e) 
				{
				}
			}
		}

		return view('entries.search', $this->getViewData([
			'search' => $search,
			'records' => $records,
			'entryTypes' => Controller::getEntryTypes(),
		]));		
	}

    public function sitemap(Request $request)
    {			
		//$server = 'http://epictravelguide.com';
		//$server = 'http://localhost';
		//$server = 'http://grittytravel.com';
		//$server = 'http://hikebikeboat.com';
		$server = 'http://scotthub.com';

		$tests = $this->makeSiteMap();
		
		return view('entries.test', $this->getViewData([
			'records' => $tests,
			'test_server' => $server,
			'executed' => null,
			'sitemap' => true,
		]));
	}
	
}
