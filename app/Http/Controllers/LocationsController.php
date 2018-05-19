<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Location;

class LocationsController extends Controller
{
    public function index()
    {
		$locations = Location::select()
			->where('user_id', '=', Auth::id())
			->orderByRaw('locations.id ASC')
			->get();
		
    	return view('locations.index', ['records' => $locations]);
    }
	
   public function view(Location $location)
    {
		$parent = Location::select()
			->where('user_id', '=', Auth::id())
			->where('id', '=', $location->parent_id)
			->first();
			
		return view('locations.view', ['record' => $location, 'parent' => $parent]);
	}

    public function activities(Location $location)
    {
		//dd($location->activities);
		
    	return view('activities.index', ['records' => $location->activities]);
    }
	
    public function add()
    {
		$locations = Location::select()
			->where('user_id', '=', Auth::id())
			->where('deleted_flag', '=', 0)
			->orderByRaw('locations.level ASC')
			->get();
	
    	return view('locations.add', ['records' => $locations]);
    }

    public function create(Request $request)
    {
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
		$locations = Location::select()
			->where('user_id', '=', Auth::id())
			->where('deleted_flag', '=', 0)
			->where('id', '<>', $location->id)
			->orderByRaw('locations.level ASC')
			->get();

    	if (Auth::check() && Auth::user()->id == $location->user_id)
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
    	if (Auth::check() && Auth::user()->id == $location->user_id)
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

    public function confirmdelete(Location $location)
    {	
    	if (Auth::check() && Auth::user()->id == $location->user_id)
        {			
			return view('locations.confirmdelete', ['record' => $location]);				
        }           
        else 
		{
             return redirect('/locations');
		}            	
    }
	
    public function delete(Location $location)
    {	
    	if (Auth::check() && Auth::user()->id == $location->user_id)
        {			
			$location->delete();
		}
		
		return redirect('/locations');
    }	
	
}
