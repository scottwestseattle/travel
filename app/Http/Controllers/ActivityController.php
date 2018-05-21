<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Activity;
use App\Photo;
use App\Location;
use DB;

class ActivityController extends Controller
{
    public function index()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$records = Activity::select()
			//->where('user_id', '=', Auth::id())
			->orderByRaw('activities.created_at DESC')
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
		$activity->map_link	 = $request->map_link;
		$activity->info_link = $request->info_link;
			
		$activity->highlights = $request->highlights;
		$activity->cost = $request->cost;
		$activity->parking = $request->parking;
		$activity->distance = $request->distance;
		$activity->difficulty = $request->difficulty;
		$activity->season = $request->season;
		$activity->wildlife = $request->wildlife;
		$activity->facilities = $request->facilities;
		$activity->elevation = $request->elevation;
		$activity->public_transportation = $request->public_transportation;
		$activity->trail_type = $request->trail_type;
		$activity->activity_type = $request->activity_type;
			
		$activity->published_flag = isset($request->published_flag) ? 1 : 0;
		$activity->approved_flag = isset($request->approved_flag) ? 1 : 0;

		$activity->user_id = Auth::id();
						
		$activity->save();
			
		return redirect(route('activity.view', [urlencode($activity->title), $activity->id]));
    }

    public function view($title, $id)
    {
		$activity = Activity::select()
			->where('deleted_flag', '<>', 1)
			->where('id', '=', $id)
			->first();
			
		$locations = $activity->locations()->orderByRaw('level ASC')->get();

		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $activity->id)
			->orderByRaw('created_at ASC')
			->get();
		
		$activity->description = nl2br($activity->description);
		$activity->description = $this->formatLinks($activity->description);
		
		return view('activities.view', ['record' => $activity, 'locations' => $locations, 'data' => $this->getViewData(), 'photos' => $photos]);
	}
	
    public function viewOrig(Activity $activity)
    {
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $activity->id)
			->orderByRaw('photos.id DESC')
			->get();
			
		//dd($photos);
		
		$activity->description = $this->formatLinks($activity->description);
		
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
			$activity->title = $request->title;
			$activity->description = $request->description;
			$activity->map_link	 = $request->map_link;
			$activity->info_link = $request->info_link;
			
			$activity->highlights = $request->highlights;
			$activity->cost = $request->cost;
			$activity->parking = $request->parking;
			$activity->distance = $request->distance;
			$activity->difficulty = $request->difficulty;
			$activity->season = $request->season;
			$activity->wildlife = $request->wildlife;
			$activity->facilities = $request->facilities;
			$activity->elevation = $request->elevation;
			$activity->public_transportation = $request->public_transportation;
			$activity->trail_type = $request->trail_type;
			$activity->activity_type = $request->activity_type;
			
			$activity->published_flag = isset($request->published_flag) ? 1 : 0;
			$activity->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$activity->save();
			
			return redirect(route('activity.view', [urlencode($activity->title), $activity->id]));
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function publish(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $activity->user_id)
        {
			return view('activities.publish', ['record' => $activity, 'data' => $this->getViewData()]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function publishupdate(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check() && Auth::user()->id == $activity->user_id)
        {			
			$published = isset($request->published_flag) ? 1 : 0;
			$activity->published_flag = $published;
			
			if ($published === 0) // if it goes back to private, then it has to be approved again
				$activity->approved_flag = 0;
			else
				$activity->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$activity->save();
			
			return redirect(route('activity.view', [urlencode($activity->title), $activity->id]));
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
	
    public function location(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		 
		$locations = Location::select()
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->orderByRaw('locations.location_type ASC')
			->get();

		$current_location = $activity->locations()->orderByRaw('location_type DESC')->first();
			
    	if (Auth::check())
        {			
			//dd($request);
			
			return view('activities.location', ['record' => $activity, 'locations' => $locations, 'current_location' => $current_location]);							
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function locationupdate(Request $request, Activity $activity)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {						
			$activity->locations()->detach();	// remove all existing locations
			$activity_id = $activity->id;
			
			$location = Location::select()
				->where('id', '=', $request->location_id)
				->first();

			if (isset($location))
			{
				$location_id = $location->id;
				$activity->locations()->save($location);
				
				$locationParent = Location::select()
					->where('id', '=', $location->parent_id)
					->first();
					
				if (isset($locationParent))
				{
					$location_parent_id = $locationParent->id;
					$activity->locations()->save($locationParent);
				}
			}
			
			return redirect(route('activity.view', [urlencode($activity->title), $activity->id]));
		}
		else
		{
			return redirect('/');
		}
    }	
	

	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////

}
