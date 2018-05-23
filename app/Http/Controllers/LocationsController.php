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
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->orderByRaw('locations.location_type ASC')
			->get();
		
    	return view('locations.index', ['records' => $locations]);
    }

    public function indexadmin()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->orderByRaw('locations.location_type ASC')
			->get();
		
    	return view('locations.indexadmin', ['records' => $locations]);
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

    public function activities(Location $location = null)
    {
		if (isset($location))
		{
			$records = $location->activities()->orderByRaw('activities.id DESC')->get();
		}
		else
		{
			$records = Activity::select()
			->where('approved_flag', '=', 1)
			->where('published_flag', '=', 1)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id DESC')
			->get();
		}
		
		foreach($records as $record)
		{
			//
			// set up tour page link and main photo
			//
			$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
			$tours_webpath = '/img/tours/';
			$link = '/view/' . $record->id;
			$photo_fullpath = $tours_fullpath . $record->id . '/';

			// get the main photo from the db
			$main_photo = $record->photos()
				->where('main_flag', 1)
				->where('deleted_flag', 0)
				->first();	
				
			if (isset($main_photo))
			{
				$main_photo = $tours_webpath . $record->id . '/' . $main_photo->filename;			
				
				$record['photo'] = $main_photo;
				$record['link'] = $link;
				//dd($main_photo);
			}		
		}

/* attempt to get all info with joins		
		$tours = DB::table('activities as a')
			//->leftJoin('photos as p', 'a.id', '=', 'p.parent_id')
			->leftJoin('activity_location as al', 'al.activity_id', '=', 'a.id')
			->leftJoin('locations as l', 'al.location_id', '=', 'l.id')
			->select('a.*', 'al.*', 'l.*'
			//	, 'p.*'
			)
			->where('al.location_id', $location->id)
			//->where('p.main_flag', '=', 1)
			//->where('a.approved_flag', '=', 1)
			//->where('a.published_flag', '=', 1)
			->where('a.deleted_flag', '=', 0)
			->orderByRaw('a.id DESC')
			->get();
					
    	return view('activities.index', ['records' => $tours]);	
*/

		// get locations so we can show the pills
		$locations = Location::select()
			//->leftJoin('locations as l1', 'l1.id', '=', 'locations.parent_id')
			->where('locations.deleted_flag', '=', 0)
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->where('popular_flag', 1)
			->orderByRaw('locations.location_type ASC')
			->get();
		
    	return view('activities.index', ['records' => $records, 'locations' => $locations]);
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
		$location->breadcrumb_flag = isset($request->breadcrumb_flag) ? 1 : 0;
		$location->popular_flag = isset($request->popular_flag) ? 1 : 0;
		
    	$location->save();
		
    	return redirect('/locations/indexadmin'); 
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
             return redirect('/locations/indexadmin');
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
			$location->breadcrumb_flag = isset($request->breadcrumb_flag) ? 1 : 0;
			$location->popular_flag = isset($request->popular_flag) ? 1 : 0;
			$location->save();
			
			return redirect('/locations/indexadmin'); 
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
             return redirect('/locations/indexadmin');
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
		
		return redirect('/locations/indexadmin');
    }	
	
}
