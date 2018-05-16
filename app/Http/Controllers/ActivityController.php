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
			
		$activity = new Activity();
		$activity->title = $request->title;
		$activity->description = $request->description;
		
		$activity->highlights = $request->highlights;
		$activity->entry_fee = $request->entry_fee;
		$activity->parking = $request->parking;
		$activity->distance = $request->distance;
		$activity->difficulty = $request->difficulty;
		$activity->season = $request->season;
		$activity->wildlife = $request->wildlife;
		$activity->facilities = $request->facilities;
		$activity->elevation_change = $request->elevation_change;
		$activity->public_transportation = $request->public_transportation;
		
		$activity->map_link = $request->map_link;
		$activity->user_id = Auth::id();
						
		$activity->save();
			
		return redirect('/activities/view/' . $activity->id);          	
    }

    public function view(Activity $activity)
    {		
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $activity->id)
			->orderByRaw('photos.id DESC')
			->get();
			
		//dd($photos);
		
		return view('activities.view', ['record' => $activity, 'data' => $this->getViewData(), 'photos' => $photos]);
	}
	
    public function edit(Request $request, Activity $activity)
    {		
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $activity->user_id)
        {
			return view('activities.edit', ['record' => $activity, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function update(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $activity->user_id)
        {
			$activity->title 					= $request->title;
			$activity->description 			= $request->description;
			$activity->map_link	 			= $request->map_link;
			
			$activity->highlights = $request->highlights;
			$activity->entry_fee = $request->entry_fee;
			$activity->parking = $request->parking;
			$activity->distance = $request->distance;
			$activity->difficulty = $request->difficulty;
			$activity->season = $request->season;
			$activity->wildlife = $request->wildlife;
			$activity->facilities = $request->facilities;
			$activity->elevation_change = $request->elevation_change;
			$activity->public_transportation = $request->public_transportation;
						
			$activity->save();
			
			return redirect('/activities/view/' . $activity->id); 
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($activity->user_id))
        {
			$activity->description = nl2br(trim($activity->description));
			
			return view('activities.confirmdelete', ['record' => $activity, 'data' => $this->getViewData(), 'referrer' => $_SERVER["HTTP_REFERER"]]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($activity->user_id))
        {			
			$activity->delete();
			
			return redirect('/activities/index');
		}
		
		return redirect('/');
    }
	
    public function viewcount(Activity $activity)
    {		
    	$activity->view_count++;
    	$activity->save();	
    	return view('activities.viewcount');
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////

}
