<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Entry;
use App\Activity;
use App\Photo;
use App\Location;
use DB;

class TourController extends Controller
{
    public function indexadmin()
    {		
		if (!$this->isAdmin())
             return redirect('/');
					
		// get the list with the location included
		$records = DB::table('entries')
			->leftJoin('activities', 'activities.parent_id', '=', 'entries.id')
			->leftJoin('locations', 'activities.location_id', '=', 'locations.id')
			->select('entries.*', 
				'activities.map_link', 
				'activities.location_id',
				'locations.name as location_name'
				)
			->where('type_flag', ENTRY_TYPE_TOUR)
			->orderByRaw('entries.published_flag ASC, entries.approved_flag ASC, activities.map_link ASC, entries.updated_at DESC') // put records with missing stuff at the top
			->get();
			
		//dd($records);
		
    	return view('tours.indexadmin', ['records' => $records]);
    }

    public function index()
    {
		$locations = null;
		$tours = $this->getIndexData($locations);

    	return view('tours.index', ['records' => $tours, 'locations' => $locations, 'page_title' => 'Tours, Hikes, Things To Do - All']);
	}		

    public function maps()
    {
		$locations = null;
		$tours = $this->getIndexData($locations);

    	return view('tours.maps', ['records' => $tours, 'locations' => $locations, 'page_title' => 'Tours, Hikes, Things To Do - Maps']);
	}		
	
    private function getIndexData(&$locations)
    {
		$tours = Activity::select()
			->where('approved_flag', '=', 1)
			->where('published_flag', '=', 1)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id DESC')
			->get();
			
		//
		// get tour page link and main photo
		//
		$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
		$tours_webpath = '/img/tours/';
		
		foreach($tours as $entry)
		{
			$link = '/view/' . $entry->id;
			$photo_fullpath = $tours_fullpath . $entry->id . '/';
			$photo = $photo_fullpath . 'main.jpg';
			$photoUc = $photo_fullpath . 'Main.jpg';
										
			// file_exists must be relative path with no leading '/'
			if (file_exists($photo) === TRUE)
			{
				// to show the photo we need the leading '/'
				$photo = $tours_webpath . $entry->id . '/main.jpg';
			}
			else if (file_exists($photoUc) === TRUE)
			{
				// to show the photo we need the leading '/'
				$photo = $tours_webpath . $entry->id . '/Main.jpg';
			}
			else
			{
				$photo = '';
				
				if (is_dir($photo_fullpath)) // if photo folder exists, get the first photo
				{
					$photos = $this->getPhotos('tours/' . $entry->id);
					if (count($photos) > 0)
						$photo = $tours_webpath . $entry->id . '/' . $photos[0];
				}
				else
				{																
					// make the folder with read/execute for everybody
					mkdir($photo_fullpath, 0755);
				}								
										
				// show the place holder
				if (strlen($photo) === 0)
					$photo = $tours_webpath . 'placeholder.jpg';
								
				$main_photo = Photo::select()
				->where('parent_id', '=', $entry->id)
				->where('main_flag', '=', 1)
				->where('deleted_flag', '=', 0)
				->first();
				
				if (isset($main_photo))
					$photo = $tours_webpath . $entry->id . '/' . $main_photo->filename;			
			}
			
			$entry['photo'] = $photo;
			$entry['link'] = $link;
		}		

		//
		// get locations so we can show the pills
		//
		$locations = Location::select()
			->where('locations.deleted_flag', '=', 0)
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->where('popular_flag', 1)
			->orderByRaw('locations.location_type ASC')
			->get();

		return $tours;
    }
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		if (Auth::check())
        {            
			return view('tours.add', ['data' => $this->getViewData()]);							
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
		$activity->user_id = Auth::id();
		$activity->published_flag = isset($request->published_flag) ? 1 : 0;
		$activity->approved_flag = isset($request->approved_flag) ? 1 : 0;
		
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
									
		$activity->save();
			
		return redirect(route('activity.view', [urlencode($activity->title), $activity->id]));
    }
	
    public function view($title, $id)
    {
		$entry = Entry::select()
			->where('type_flag', ENTRY_TYPE_TOUR)
			->where('deleted_flag', '<>', 1)
			->where('id', $id)
			->first();			
			
		$activity = Activity::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', $entry->id)
			->first();			
			
		$locations = DB::table('activities')
			->leftJoin('locations as l1', 'activities.location_id', '=', 'l1.id')
			->leftJoin('locations as l2', 'l1.parent_id', '=', 'l2.id')
			->leftJoin('locations as l3', 'l2.parent_id', '=', 'l3.id')
			->leftJoin('locations as l4', 'l3.parent_id', '=', 'l4.id')
			->leftJoin('locations as l5', 'l4.parent_id', '=', 'l5.id')
			->leftJoin('locations as l6', 'l5.parent_id', '=', 'l6.id')
			->leftJoin('locations as l7', 'l6.parent_id', '=', 'l7.id')
			->leftJoin('locations as l8', 'l7.parent_id', '=', 'l8.id')
			->select(
				  'l1.name as loc1', 'l1.id as loc1_id', 'l1.breadcrumb_flag as loc1_breadcrumb_flag'
				, 'l2.name as loc2', 'l2.id as loc2_id', 'l2.breadcrumb_flag as loc2_breadcrumb_flag'
				, 'l3.name as loc3', 'l3.id as loc3_id', 'l3.breadcrumb_flag as loc3_breadcrumb_flag'
				, 'l4.name as loc4', 'l4.id as loc4_id', 'l4.breadcrumb_flag as loc4_breadcrumb_flag'
				, 'l5.name as loc5', 'l5.id as loc5_id', 'l5.breadcrumb_flag as loc5_breadcrumb_flag'
				, 'l6.name as loc6', 'l6.id as loc6_id', 'l6.breadcrumb_flag as loc6_breadcrumb_flag'
				, 'l7.name as loc7', 'l7.id as loc7_id', 'l7.breadcrumb_flag as loc7_breadcrumb_flag'
				, 'l8.name as loc8', 'l8.id as loc8_id', 'l8.breadcrumb_flag as loc8_breadcrumb_flag'
				)
			->where('activities.id', $activity->id)
			->first();

		$location = array();
		if (isset($locations->loc1))
		{
			$location[0]['name'] = $locations->loc1;
			$location[0]['id'] = $locations->loc1_id;
			$location[0]['breadcrumb_flag'] = $locations->loc1_breadcrumb_flag;
		}
		if (isset($locations->loc2) && $locations->loc2_breadcrumb_flag == 1)
		{
			$location[1]['name'] = $locations->loc2;
			$location[1]['id'] = $locations->loc2_id;
			$location[1]['breadcrumb_flag'] = $locations->loc2_breadcrumb_flag;
		}
		if (isset($locations->loc3) && $locations->loc3_breadcrumb_flag == 1)
		{
			$location[2]['name'] = $locations->loc3;
			$location[2]['id'] = $locations->loc3_id;
			$location[2]['breadcrumb_flag'] = $locations->loc3_breadcrumb_flag;
		}
		if (isset($locations->loc4) && $locations->loc4_breadcrumb_flag == 1)
		{
			$location[3]['name'] = $locations->loc4;
			$location[3]['id'] = $locations->loc4_id;
			$location[3]['breadcrumb_flag'] = $locations->loc4_breadcrumb_flag;
		}
		if (isset($locations->loc5) && $locations->loc5_breadcrumb_flag == 1)
		{
			$location[4]['name'] = $locations->loc5;
			$location[4]['id'] = $locations->loc5_id;
			$location[4]['breadcrumb_flag'] = $locations->loc5_breadcrumb_flag;
		}
		if (isset($locations->loc6) && $locations->loc6_breadcrumb_flag == 1)
		{
			$location[5]['name'] = $locations->loc6;
			$location[5]['id'] = $locations->loc6_id;
			$location[5]['breadcrumb_flag'] = $locations->loc6_breadcrumb_flag;
		}
		if (isset($locations->loc7) && $locations->loc7_breadcrumb_flag == 1)
		{
			$location[6]['name'] = $locations->loc7;
			$location[6]['id'] = $locations->loc7_id;
			$location[6]['breadcrumb_flag'] = $locations->loc7_breadcrumb_flag;
		}
		if (isset($locations->loc8) && $locations->loc8_breadcrumb_flag == 1)
		{
			$location[7]['name'] = $locations->loc8;
			$location[7]['id'] = $locations->loc8_id;
			$location[7]['breadcrumb_flag'] = $locations->loc8_breadcrumb_flag;
		}
		
		// this happens if the location structure has been changed, the hasMany table needs to be updated
		if (count($location) != $activity->locations()->count())
		{
			//
			// remove all current locations so they can be replaced
			//
			$activity->locations()->detach();
			
			//
			// save current structure
			//
			$this->saveLocations($activity, $locations);
		}
					
		//dd($location);
					
		$photos = Photo::select()
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();

		// update the view count for new visitors only
		if ($this->isNewVisitor())
		{
			$entry->view_count++;
			$entry->save();
		}
		
		$entry->description = nl2br($entry->description);
		$entry->description = $this->formatLinks($entry->description);		
		
		return view('tours.view', ['record' => $entry, 'activity' => $activity, 'locations' => array_reverse($location), 'data' => $this->getViewData(), 'photos' => $photos, 'page_title' => $entry->title]);
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
		
		return view('tours.view', ['record' => $activity, 'data' => $this->getViewData(), 'photos' => $photos]);
	}
	
    public function edit($id)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		
		$record = DB::table('entries')
			->leftJoin('activities', 'activities.parent_id', '=', 'entries.id')
			->select('entries.*', 'entries.description_short as highlights',
				'activities.map_link', 'activities.location_id', 'activities.season', 'activities.wildlife', 
				'activities.public_transportation', 'activities.facilities', 'activities.parking', 'activities.cost', 
				'activities.distance', 'activities.difficulty', 'activities.elevation', 'activities.trail_type'
				)
			->where('type_flag', ENTRY_TYPE_TOUR)
			->where('entries.id', $id)
			->first();		
		
    	if ($this->isOwnerOrAdmin($record->user_id))
        {
			return view('tours.edit', ['record' => $record]);
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

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {
			$entry->title = $request->title;
			$entry->description = $request->description;
			$entry->description_short = $request->description_short;
			$entry->published_flag = isset($request->published_flag) ? 1 : 0;
			$entry->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$entry->save();
			
			$activity = Activity::select()
				->where('deleted_flag', '<>', 1)
				->where('parent_id', $entry->id)
				->first();
	
			if (!isset($activity))
			{
				$activity = new Activity();
				$activity->user_id = Auth::id();
				//$activity->site_id	 = $this->getSiteId();
				$activity->parent_id = $entry->id;
				$activity->title = 'parent_id = ' . $entry->id;
			}
			
			$activity->map_link	 = $request->map_link;
			$activity->info_link = $request->info_link;
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
						
			$activity->save();
			
			return redirect(route('tour.view', [urlencode($entry->title), $entry->id]));
		}
		else
		{
			return redirect('/');
		}
    }	
	
    public function confirmdelete(Entry $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if ($this->isOwnerOrAdmin($entry->user_id))
        {
			$entry->description = nl2br(trim($entry->description));
			
			return view('tours.confirmdelete', ['record' => $entry]);
        }           
        else 
		{
             return redirect('/');
		}            	
    }
	
    public function delete(Request $request, Activity $entry)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {			
			$entry->delete();
			
			return redirect('/tours/index');
		}
		
		return redirect('/');
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
			
			return view('tours.location', ['record' => $activity, 'locations' => $locations, 'current_location' => $current_location]);							
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
			$activity_id = $activity->id;
			$location_id = intval($request->location_id);

			if ($location_id <= 0)
			{
				//
				// location is being removed
				//
				
				// remove the hasMnay
				$activity->locations()->detach();
				
				// remove the hasOne
				$activity->location_id = null;
				$activity->save();
			}
			else
			{
				// get the new location
				$location = Location::select()
					->where('id', '=', $location_id)
					->first();

				if (isset($location))
				{
					//
					// remove all current locations so they can be replaced
					//
					$activity->locations()->detach();
					
					//
					// set the primary has-one location
					//
					$activity->location_id = $location_id;
					$activity->save();
					
					//
					// set the hasmany locations
					//
					
					$locations = DB::table('activities')
						->leftJoin('locations as l1', 'activities.location_id', '=', 'l1.id')
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
						->where('activities.id', $activity->id)
						->first();
					//dd($locations);
					
					$this->saveLocations($activity, $locations);
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

    private function saveLocations($activity, $locations)
    {	
		$this->saveLocation($activity, $locations->loc1_id);
		$this->saveLocation($activity, $locations->loc2_id);
		$this->saveLocation($activity, $locations->loc3_id);
		$this->saveLocation($activity, $locations->loc4_id);
		$this->saveLocation($activity, $locations->loc5_id);
		$this->saveLocation($activity, $locations->loc6_id);
		$this->saveLocation($activity, $locations->loc7_id);
		$this->saveLocation($activity, $locations->loc8_id);
	}

    private function saveLocation($activity, $id)
    {	
		if (isset($id))
		{
			$record = Location::select()
					->where('id', '=', $id)
					->first();
			//dd($record);
					
			if (isset($record))
			{
				$activity->locations()->save($record);
			}
			else
			{
				dd('location record not found');
			}
		}
		else
		{
			// valid condition because the records don't have all location levels
		}	
	}
}
