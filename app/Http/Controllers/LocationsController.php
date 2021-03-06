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
		
		$vdata = $this->getViewData([
			'records' => $locations, 'page_title' => 'Locations'
		]);
		
		
    	return view('locations.index', $vdata);
    }

    public function indexadmin()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			->where('deleted_flag', 0)
			->orderByRaw('locations.name ASC')
			->get();
		
		$vdata = $this->getViewData([
			'records' => $locations, 
			'page_title' => 'Locations',
		]);
		
    	return view('locations.indexadmin', $vdata);
    }
	
    public function activities(Location $location = null)
    {
		$location_name = '';
		if (isset($location))
		{
			$location_name = ' - ' . $location->name;
			$records = $location->activities()->orderByRaw('activities.id DESC')->get();
		}
		else
		{
			// get all
			$records = Activity::select()
				//->where('site_id', SITE_ID)
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
			}		
		}

		// get locations so we can show the pills
		$locations = Location::select()
			//->leftJoin('locations as l1', 'l1.id', '=', 'locations.parent_id')
			->where('locations.deleted_flag', '=', 0)
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->where('popular_flag', 1)
			->orderByRaw('locations.location_type ASC')
			->get();
		
		$vdata = $this->getViewData([
			'records' => $records, 'locations' => $locations, 'page_title' => 'Tours, Hikes, Things To Do' . $location_name
		]);
		
    	return view('activities.index', $vdata);
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
		$locWithParent = $this->getLocation($location->id, $activities);			

		$vdata = $this->getViewData([
			'record' => $locWithParent,
			'activities' => $activities,
			'entries' => $location->entries,
		]);		
			
		return view('locations.view', $vdata);
	}
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$locations = Location::select()
			//->where('user_id', '=', Auth::id())
			->where('deleted_flag', '=', 0)
			->orderByRaw('locations.name ASC')
			->get();	
		
    	return view('locations.add', $this->getViewData([
			'records' => $locations, 
			]));
    }

    public function exists($name, $parent_id)
    {		
		$l = Location::select()
			->where('deleted_flag', '=', 0)
			->where('parent_id', $parent_id)
			->where('name', $name)
			->first();
			
		return (isset($l));
	}
	
    public function create(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		if ($this->exists($request->name, $request->parent_id) > 0)
		{
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', 'Location Already Exists: ' . $request->name);		

	    	return redirect('/locations/add'); 
		}
		
    	$location = new Location();
    	$location->name = $request->name;
    	$location->parent_id = $request->parent_id;
    	$location->user_id = Auth::id();
		$location->location_type = $request->location_type;
		$location->breadcrumb_flag = isset($request->breadcrumb_flag) ? 1 : 0;
		$location->popular_flag = isset($request->popular_flag) ? 1 : 0;
		
    	$location->save();

		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', 'Location Added: ' . $request->name);		
		
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
			return view('locations.edit', $this->getViewData([
				'location' => $location,
				'records' => $locations, 
				]));			
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
			if (strtolower($location->name) != strtolower($request->name) && $this->exists($request->name, $request->parent_id) > 0)
			{
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', 'Location Already Exists: ' . $request->name);		

				return redirect('/locations/edit/' . $location->id); 
			}
		
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
			$locWithParent = $this->getLocation($location->id, $activities);
			
			return view('locations.confirmdelete', $this->getViewData([
				'record' => $locWithParent,
				'entries' => $location->entries, 
				'activities' => $activities,
				]));	
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
			$location->deleteSafe();
		}
		
		return redirect('/locations/indexadmin');
    }	
	
}
