<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Entry;
use App\Photo;
use DB;

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
			->where('site_id', $this->getSiteId())
			//->where('type_flag', '<>', ENTRY_TYPE_TOUR)
			->where('deleted_flag', 0)
			->orderByRaw('entries.id DESC')
			->get();
		
    	return view('entries.index', ['records' => $entries]);
    }

    public function indexadmin()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$entries = Entry::select()
			->where('site_id', $this->getSiteId())
			//->where('type_flag', '<>', ENTRY_TYPE_TOUR)
			->where('deleted_flag', 0)
			->orderByRaw('entries.id DESC')
			->get();
			
		$entries = DB::select('
			SELECT entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.published_flag, entries.approved_flag, entries.updated_at,
				count(photos.id) as photo_count
			FROM entries
			LEFT JOIN photos
				ON photos.parent_id = entries.id AND photos.deleted_flag = 0
			WHERE 1=1
				AND entries.deleted_flag = 0
			GROUP BY entries.id, entries.type_flag, entries.view_count, entries.title, entries.description, entries.published_flag, entries.approved_flag, entries.updated_at
			ORDER BY entries.published_flag ASC, entries.approved_flag ASC, entries.updated_at DESC
		' , []);			
		
    	return view('entries.indexadmin', ['records' => $entries]);
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
			->where('site_id', '=', $this->getSiteId())
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

			 if (Auth::check())
        {            	
			return view('entries.add', ['current_type' => ENTRY_TYPE_ARTICLE]);							
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
           
			//dd($request);
			
		$entry = new Entry();
		
		$entry->site_id = 1;
		$entry->user_id = Auth::id();
		$entry->type_flag = $request->type_flag;
		$entry->title = $request->title;
		$entry->description = $request->description;
		$entry->description_short = $request->description_short;
						
		$entry->save();
			
		return redirect('/entries/show/' . $entry->id);          	
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

    public function view($title, $id)
    {
		$entry = Entry::select()
			->where('site_id', $this->getSiteId())
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
		$entry = Entry::select()
			->where('site_id', $this->getSiteId())
			->where('deleted_flag', 0)
			->where('id', $id)
			->first();
		
		$photos = Photo::select()
			->where('deleted_flag', 0)
			->where('parent_id', $entry->id)
			->orderByRaw('id ASC')
			->get();
		
		return view('entries.view', ['record' => $entry, 'photos' => $photos]);
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
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {				
			$entry->type_flag 			= $request->type_flag;	
			$entry->title 				= $request->title;
			$entry->description_short	= $request->description_short;
			$entry->description			= $request->description;
			
			$entry->save();
			
			return redirect('/entries/show/' . $entry->id); 
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
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {
			return view('entries.publish', ['record' => $entry, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
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
			
			$entry->view_count = intval($request->view_count);
			
			$entry->save();
			
			return redirect(route('entry.view', [urlencode($entry->title), $entry->id]));
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
