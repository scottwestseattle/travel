<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Activity;
use App\Photo;
use DB;

class ActivityController extends Controller
{
    public function index()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$records = Activity::select()
			->where('user_id', '=', Auth::id())
			->orderByRaw('activities.id DESC')
			->get();
		
    	return view('activities.index', compact('records'));
    }

    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		if (Auth::check())
        {            
			return view('activities.add', ['data' => $this->getViewData()]);							
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
			
		$record = new Activity();
		$record->title = $request->title;
		$record->description = $request->description;
		$record->map_link = $request->map_link;
		$record->user_id = Auth::id();
						
		$record->save();
			
		return redirect('/activities/view/' . $record->id);          	
    }

    public function upload(Activity $record)
    {
		if (!$this->isAdmin())
             return redirect('/');
			 
    	if (Auth::check())
        {            
			//todo $categories = Category::lists('title', 'id');
	
			return view('activities.upload', ['record' => $record, 'data' => $this->getViewData()]);
        }           
        else 
		{
             return redirect('/');
        }       
	}
	
    public function store(Request $request, Activity $record)
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
				return view('activities.upload', ['record' => $record, 'data' => $this->getViewData()]);	
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
				return view('activities.upload', ['record' => $record, 'data' => $this->getViewData()]);					
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
							
			$path = base_path() . TOUR_PHOTOS_PATH . $record->id;
			
			//dd($name);
			
			$request->file('image')->move($path, $name);
						
			return redirect('/activities/view/' . $record->id);
        }           
        else 
		{
             return redirect('/');
        }            	
    }

    public function view(Activity $record)
    {
		dd($record);
		
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $record->id)
			->orderByRaw('photos.id DESC')
			->get();
			
		//dd($photos);
		
		return view('activities.view', ['record' => $record, 'data' => $this->getViewData(), 'photos' => $photos]);
	}

    public function home()
    {
		if (!$this->isAdmin())
             return redirect('/');

		return $this->index();
	}
	
    public function edit(Request $request, Activity $record)
    {
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $record->user_id)
        {
			return view('activities.edit', ['record' => $record, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request, Activity $record)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $record->user_id)
        {
			$record->title 					= $request->title;
			$record->description 			= $request->description;
			$record->map_link	 			= $request->map_link;
			$record->save();
			
			return redirect('/activities/view/' . $record->id); 
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Request $request, Activity $record)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($record->user_id))
        {
			$record->description = nl2br($this->fixEmpty(trim($record->description), EMPTYBODY));
			
			return view('activities.delete', ['record' => $record, 'data' => $this->getViewData(), 'referrer' => $_SERVER["HTTP_REFERER"]]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Activity $record)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($record->user_id))
        {
			//dd($record);
			
			$record->delete();
			
			return redirect('/index');
		}
		
		return redirect('/');
    }
	
    public function viewcount(Activity $record)
    {		
    	$record->view_count++;
    	$record->save();	
    	return view('activities.viewcount');
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////

}
