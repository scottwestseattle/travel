<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Location;
use App\Activity;
use DB;

class LocationsController extends Controller
{
    public function index()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->orderByRaw('locations.location_type ASC')
			->get();
		
    	return view('locations.index', ['records' => $locations]);
    }
	
   public function view(Location $location)
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$parent = Location::select()
			//->where('user_id', '=', Auth::id())
			->where('id', '=', $location->parent_id)
			->first();
			
		$activities = null;
		$location = $this->getLocation($location->id, $activities);			
			
		return view('locations.view', ['record' => $location, 'activities' => $activities]);
	}

    public function activities(Location $location)
    {
		//dd($location->activities[0]->location);
		
    	return view('activities.index', ['records' => $location->activities]);
    }
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->where('deleted_flag', '=', 0)
			->orderByRaw('locations.level ASC')
			->get();
	
    	return view('locations.add', ['records' => $locations]);
    }

    public function create(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
		
    	$location = new Location();
    	$location->name = $request->name;
    	$location->parent_id = $request->parent_id;
    	$location->user_id = Auth::id();
		$location->location_type = $request->location_type;
		
    	$location->save();
		
    	return redirect('/locations'); 
    }

    public function edit(Location $location)
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->where('deleted_flag', '=', 0)
			->where('id', '<>', $location->id)
			->orderByRaw('locations.level ASC')
			->get();

    	if (Auth::check())
        {
			return view('locations.edit', ['location' => $location, 'records' => $locations]);			
        }
        else 
		{
             return redirect('/locations');
		}            	
    }
	
    public function update(Request $request, Location $location)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if (Auth::check())
        {
			$location->name = $request->name;
			$location->location_type = $request->location_type;
			$location->parent_id = $request->parent_id;
			$location->save();
			
			return redirect('/locations/'); 
		}
		else
		{
			return redirect('/');
		}
    }

    private function getLocation($id, &$activities)
    {				
		$location = DB::table('locations')
			->leftJoin('locations as parent', 'locations.parent_id', '=', 'parent.id')
			->select('locations.*', 'parent.name as parent_name', 'parent.id as parent_id')
			->where('locations.id', $id)
			->first();
			
		//dd($location);
			
		$activities = Activity::select()
			->where('deleted_flag', '<>', 1)
			->where('location_id', '=', $id)
			->get();
			
		return $location;
	}
	
    public function confirmdelete(Location $location)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {
			$activities = null;
			$location = $this->getLocation($location->id, $activities);
			
			return view('locations.confirmdelete', ['record' => $location, 'activities' => $activities]);
        }
        else 
		{
             return redirect('/locations');
		}            	
    }
	
    public function delete(Location $location)
    {
		if (!$this->isAdmin())
             return redirect('/');
		
    	if (Auth::check())
        {			
			$location->delete();
		}
		
		return redirect('/locations');
    }	
	
}
