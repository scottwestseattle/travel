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
		
    	return view('entries.index', ['records' => $entries]);
    }

    public function indexadmin($type_flag = null)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::getEntriesByType($type_flag, /* approved = */ false);

		$vdata = [
			'records' => $entries,
			'redirect' => '/entries/indexadmin',
			'typeNames' => $this->typeNames,
		];
		
    	return view('entries.indexadmin', $vdata);
    }
	
    public function tag($tag_id)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('user_id', '=', Auth::id())
			//->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
		
    	return view('entries.index', compact('entries'));
    }

    public function posts()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		//dd($entries);
		
		return view('entries.index', ['entries' => $entries, 'data' => $this->getViewData(), 'title' => 'Posts']);
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
		
		return view('entries.index', ['records' => $entries]);
    }
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		return view('entries.add');							
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
		$entry->permalink			= $this->trimNull($request->permalink);
		$entry->description_short	= $this->trimNull($request->description_short);
		$entry->description			= $this->trimNull($request->description);
		$entry->display_date		= $request->display_date;

		$entry->save();
			
		return redirect($this->getReferer($request, '/entries/show/' . $entry->id)); 
    }

    public function upload(Entry $entry)
    {
		if (!$this->isAdmin())
             return redirect('/');
			 
    	if (Auth::check())
        {            
			//todo $categories = Category::lists('title', 'id');
	
			return view('entries.upload', ['entry' => $entry, 'data' => $this->getViewData()]);
        }           
        else 
		{
             return redirect('/');
        }       
	}
	
    public function store(Request $request, Entry $entry)
    {		
		if (!$this->isAdmin())
             return redirect('/');

			 if (Auth::check())
        {            
			//dd($request->file('image'));
				
			//
			// get file to upload
			//
			$file = $request->file('image');
			if (!isset($file))
			{
				// bad or missing file name
				return view('entries.upload', ['entry' => $entry, 'data' => $this->getViewData()]);	
			}
			
			//
			// get and check file extension
			//
			$ext = strtolower($file->getClientOriginalExtension());
			if (isset($ext) && $ext === 'jpg')
			{
			}
			else
			{
				// bad or missing extension
				return view('entries.upload', ['entry' => $entry, 'data' => $this->getViewData()]);					
			}
						
			//
			// get and check new file name
			//
			$name = trim($request->name);
			if (isset($name) && strlen($name) > 0)
			{
				$name = preg_replace('/[^\da-z ]/i', ' ', $name); // remove all non-alphanums
				$name = str_replace(" ", "-", $name);			// replace spaces with dashes
			}
			else
			{
				// no file name given so name it with timestamp
				$name = date("Ymd-His");
			}

			$name .= '.' . $ext;
							
			$path = base_path() . TOUR_PHOTOS_PATH . $entry->id;
			
			//dd($name);
			
			$request->file('image')->move($path, $name);
						
			return redirect('/entries/view/' . $entry->id);
        }           
        else 
		{
             return redirect('/');
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
						
		if (!isset($entry))
		{
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', 'Permalink Entry Not Found: ' . $permalink);
            return redirect('/entries/index');
		}
		
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			$next = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
				->where('display_date', '>', $entry->display_date)
				->orderByRaw('display_date ASC')
				->first();

			$prev = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
				->where('display_date', '<', $entry->display_date)
				->orderByRaw('display_date DESC')
				->first();
		}
			
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
			
		$vdata = [
			'record' => $entry, 
			'next' => $next,
			'prev' => $prev,
			'photos' => $photos,
		];
		
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
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
		
		return view('entries.view', ['record' => $entry, 'photos' => $photos]);
	}

    public function show($id)
    {
		$next = null;
		$prev = null;
		
		$entry = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('id', $id)
			->first();
			
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			$next = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
				->where('display_date', '>', $entry->display_date)
				->orderByRaw('display_date ASC')
				->first();

			$prev = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
				->where('display_date', '<', $entry->display_date)
				->orderByRaw('display_date DESC')
				->first();
		}
			
		$photos = Photo::select()
			->where('deleted_flag', 0)
			->where('parent_id', $entry->id)
			->orderByRaw('id ASC')
			->get();
			
		$vdata = [
			'record' => $entry, 
			'next' => $next,
			'prev' => $prev,
			'photos' => $photos,
		];			
		
		return view('entries.view', $vdata);
	}
	
    public function home()
    {
		if (!$this->isAdmin())
             return redirect('/');

		return $this->index();
	}
	
    public function edit(Entry $entry)
    {		
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			return view('entries.edit', ['record' => $entry]);
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request, Entry $entry)
    {
		$record = $entry;
		
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $record->user_id)
        {				
			$record->type_flag 			= $request->type_flag;
			$record->title 				= trim($request->title);
			$record->permalink			= trim($request->permalink);
			$record->description_short	= trim($request->description_short);
			$record->description		= trim($request->description);
			$record->display_date		= $request->display_date;
			
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
			$entry->description = nl2br($this->fixEmpty(trim($entry->description), EMPTYBODY));
			$entry->description_language1 = nl2br($this->fixEmpty(trim($entry->description_language1), EMPTYBODY));
			
			if ($entry->type_flag === ENTRY_TYPE_TOUR)
				return view('tours.confirmdelete', ['record' => $entry]);			
			else
				return view('entries.delete', ['entry' => $entry]);
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

		return view('entries.publish', ['record' => $entry]);
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
			return view('entries.location', ['record' => $entry, 'locations' => $locations, 'current_location' => $current_location]);							
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
	
	private function merge_entry(Entry $entry)
    {
		if (intval($entry->is_template_flag) === 0)
		{				
			// get the template
			$template = $this->getDefaultTemplate();
				
			if ($template !== null)
			{	
				$descriptionCopy = $this->merge($template->description, $entry->description);		
				$descriptionCopy2 = $this->merge($template->description_language1, $entry->description_language1);
					
				$entry->description = $this->merge($template->description, $entry->description, /* style = */ true);			
				$entry->description_language1 = $this->merge($template->description_language1, $entry->description_language1, /* style = */ true);
				//dd($entry->description);

				$data = compact('entry');
				$data['description_copy'] = $descriptionCopy;
				$data['description_copy2'] = $descriptionCopy2;					
				//dd($data);
				
				return $data;
			}
			else
			{
			}
		}
		else
		{
		}	
			
		$entry->description = nl2br($entry->description);
		$entry->description_language1 = nl2br($entry->description_language1);
					
		$data = compact('entry');
		$data['description_copy'] = $entry->description;
		$data['description_copy2'] = $entry->description_language1;
		
		return $data;
	}

	private function getDefaultTemplate()
	{
		$template_id = intval(Auth::user()->template_id);
				
		if ($template_id === 0) // default template not set, use first template
		{
			$entry = Entry::select()
				->where('user_id', '=', Auth::id())
				->where('is_template_flag', '=', 1)
				->first();
		}
		else // get user's default template
		{
			$entry = Entry::select()
				->where('user_id', '=', Auth::id())
				->where('is_template_flag', '=', 1)
				->where('id', '=',  $template_id)
				->first();
		}
		
		return $entry;
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
