<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Entry;
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
    	//$user = Auth::user(); // original gets current user with all entries
		
		$entries = Entry::select()
			->where('user_id', '=', Auth::id())
			//->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id')
			->get();
			
		//dd($entries);
		
    	return view('entries.index', compact('entries'));
    }

    public function posts()
    {
    	//$user = Auth::user(); // original gets current user with all entries
		
		$entries = Entry::select()
			->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		//dd($entries);
		
    	return view('entries.index', compact('entries'));
    }
	
    public function tours()
    {
		$entries = Entry::select()
			->where('user_id', '=', Auth::id())
			->where('is_template_flag', '=', 1)
			->orderByRaw('entries.id DESC')
			->get();
		
		return view('entries.index', ['entries' => $entries, 'data' => $this->getViewData()]);									
//    	return view('entries.index', compact('entries'));
    }
	
    public function add()
    {
    	if (Auth::check())
        {            
			//todo $categories = Category::lists('title', 'id');
	
			return view('entries.add', ['data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
        }       
	}

    public function create(Request $request)
    {		
    	if (Auth::check())
        {            
			//dd($request);
			
			$entry = new Entry();
			$entry->title = $request->title;
			$entry->description = $request->description;
			$entry->map_link = $request->map_link;
			$entry->description_language1 = $request->description_language1;
			$entry->is_template_flag = (isset($request->is_template_flag)) ? 1 : 0;
			//$entry->uses_template_flag = (isset($request->uses_template_flag)) ? 1 : 0; //sbw
			$entry->user_id = Auth::id();
			
			//dd($entry);		
			
			$entry->save();
			
			return redirect('/entries/gen/' . $entry->id);
        }           
        else 
		{
             return redirect('/');
        }            	
    }

    public function view(Entry $entry)
    {
		//dd('here');
		return view('entries.view', compact('entry'));
    }

    public function gen(Entry $entry)
    {		
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			$entry = $this->merge_entry($entry);
			$entry['data'] = $this->getViewData();

			return view('entries.gen', $entry);			
        }           
        else 
		{
             return redirect('/');
		}            	
    }

    public function settemplate($id)
    {
		$id = (intval($id) >= 0) ? intval($id) : 0;

		// if id set and it is changing
		if ($id > 0 && Auth::check() && intval(Auth::user()->template_id) != $id)
		{
			// template is changing
			//dd('template change');
			
			Auth::user()->template_id = $id;
			Auth::user()->save();
		}

    	return view('entries.settemplate');
	}

    public function home()
    {
		return $this->index();
	}
	
    public function gendex($id = INTNOTSET)
    {
		$id = (intval($id) >= 0) ? intval($id) : 0;
		
		if (Auth::check())
        {			
			//
			// get the template or entry to show
			//
			if ($id > 0) // get the specified article
			{
				$entry = Entry::select()
					->where('user_id', '=', Auth::id())
					->where('id', '=' , $id)
					->first();
				
				if ($entry !== null)
				{
					$data = $this->merge_entry($entry);
					$data['entry']['description'] = $this->formatLinks($data['entry']['description']);
					$data['entry']['description_language1'] = $this->formatLinks($data['entry']['description_language1']);
				}
				else
				{
					// entry doesn't exist or it's not their entry
					$this->index();
				}
			}
			else // get the default template
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

				if ($entry === null)
				{	
					// new user won't have any data					
					$entry = new Entry();
					$entry->title = "Standard Layout";
					$entry->description = BODY_PLACEHODER;
					$entry->description_language1 = BODY_PLACEHODER;
					$entry->is_template_flag = 1;
					$entry->user_id = Auth::id();
								
					$entry->save();					
				}

				$entry->description = str_replace(BODY_PLACEHODER, $this->fixEmpty('', BODY), $entry->description) . '<br/>';
				$entry->description_language1 = str_replace(BODY_PLACEHODER, $this->fixEmpty('', BODY), $entry->description_language1) . '<br/>';
					
				$data = $this->merge_entry($entry);	
			}			
			
			//
			// get entry list
			//
			$entries = Entry::select()
				->where('user_id', '=', Auth::id())
				->where('is_template_flag', '<>', 1)
				->where('view_count', '>=', 0)
				->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')				
				//->orderBy('title')
				->limit(25)
				->get();

			//
			// get template list
			//
			$templates = Entry::select('id', 'title')
				->where('user_id', '=', Auth::id())
				->where('is_template_flag', '=', 1)
				->orderBy('title')
				->get();
			
			$data['entries'] = $entries;
			$data['templates'] = $templates;
			$data['data'] = $this->getViewData();
						
			if ($entry === null)
			{
				// no entries, take them to index
				return view('entries.index', compact('entries'));
			}
			else
			{
				// show default or selected entry
				return view('entries.gendex', $data);
			}
        }          	
    }
	
    public function edit(Request $request, Entry $entry)
    {
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			//dd($entry);
			// flags come from dev mysql as ints and prod mysql as strings
			$entry['is_template'] = (intval($entry->is_template_flag) === 1);

			return view('entries.edit', ['entry' => $entry, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request, Entry $entry)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			//dd($request);
				
			$entry->title 					= $request->title;
			$entry->description 			= $request->description;
			$entry->map_link	 			= $request->map_link;
			$entry->description_language1 	= $request->description_language1;
			$entry->is_template_flag 		= isset($request->is_template_flag) ? 1 : 0;
			$entry->save();
			
			return redirect('/entries/gen/' . $entry->id); 
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Request $request, Entry $entry)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			$entry->description = nl2br($this->fixEmpty(trim($entry->description), EMPTYBODY));
			$entry->description_language1 = nl2br($this->fixEmpty(trim($entry->description_language1), EMPTYBODY));
			
			return view('entries.delete', ['entry' => $entry, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Entry $entry)
    {	
    	if (Auth::check() && Auth::user()->id == $entry->user_id)
        {
			//dd($entry);
			
			$entry->delete();
			
			return redirect('/entries/index/');
			
		}
		
		return redirect('/entries/index');
    }
		
    public function search($search)
    {
		$rc = 0;
		$userId = 1;
		$entries = null;

		if (mb_strlen($search) > 0)
		{
			// strip everything except alpha-numerics, colon, and spaces
			$search = preg_replace("/[^:a-zA-Z0-9 .]+/", "", $search);
		}
		else
		{
			echo 'no search string';
			return $rc;
		}

		if (mb_strlen($search) == 0)
		{
			echo 'no search string';
			return $rc;
		}

		$entries = Entry::select()->whereRaw('1 = 1')
			->where('user_id', '=', Auth::id())
			->where('is_template_flag', '=', 0)
			->where(function ($query) use ($search) {
				return $query
					->where('title', 'like', '%' . $search . '%')
					->orWhere('description', 'like', '%' . $search . '%')
					->orWhere('description_language1', 'like', '%' . $search . '%')
			;})
			->orderBy('title')
			->limit(25)
			->get();

		$entries = compact('entries');

		//dd($entries);
				
    	return view('entries.search', $entries);
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
	
    public function timer()
    {
		return view('entries.timer');
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
