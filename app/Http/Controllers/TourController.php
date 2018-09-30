<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Entry;
use App\Activity;
use App\Photo;
use App\Location;
use DB;

define('PREFIX', 'tours');
define('LOG_MODEL', 'tours');
define('TITLE', 'Tours');

class TourController extends Controller
{
    public function indexadmin()
    {		
		if (!$this->isAdmin())
             return redirect('/');
					
		$records = $this->getTourIndexAdmin();
					
		$vdata = $this->getViewData([
			'records' => $records,
		]);
		
    	return view('tours.indexadmin', $vdata);
    }

    public function index()
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);

		$showAll = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true);	
		
		//$tours = $this->getTourIndex(/* $allSites = */ true);
		$tours = $this->getEntriesByType(ENTRY_TYPE_TOUR);

		$tour_count = isset($tours) ? count($tours) : 0;
		$locations = Location::getPills();
			
		$photo_path = '/img/entries/';
		
		$vdata = $this->getViewData([
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'locations' => $locations, 
			'showAll' => $showAll, 
			'photo_path' => $photo_path, 
			'page_title' => 'Tours, Hikes, Things To Do',
		]);
				
    	return view('tours.index', $vdata);
	}		

    public function maps()
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_MAPS);

		$locations = null;
		$tours = $this->getIndexData($locations);

    	return view('tours.maps', ['records' => $tours, 'locations' => $locations, 'page_title' => 'Tours, Hikes, Things To Do - Maps']);
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
			
		$success = false;
		
		try {
			DB::beginTransaction();	

			//
			// the entry part
			//
			$entry = new Entry();
			$entry->site_id = SITE_ID;
			$entry->user_id = Auth::id();
			$entry->type_flag = ENTRY_TYPE_TOUR;
			$entry->published_flag = isset($request->published_flag) ? 1 : 0;
			$entry->approved_flag = isset($request->approved_flag) ? 1 : 0;

			// user set
			$entry->title = trim($request->title);
			$entry->permalink = trim($request->permalink);
			$entry->description = trim($request->description);
			$entry->description_short = trim($request->description_short);

			$entry->save();
			
			//
			// the activity part
			//
			$activity = new Activity();
			$activity->user_id = Auth::id();
			$activity->site_id = SITE_ID;
			
			$activity->map_link	 	= trim($request->map_link);
			$activity->map_label	= trim($request->map_label);
			$activity->map_labelalt	= trim($request->map_labelalt);
			
			$activity->map_link2	 = trim($request->map_link2);
			$activity->map_label2	 = trim($request->map_label2);
			$activity->map_labelalt2 = trim($request->map_labelalt2);
			
			$activity->info_link 	= trim($request->info_link);
				
			$activity->cost = trim($request->cost);
			$activity->parking = trim($request->parking);
			$activity->distance = trim($request->distance);
			$activity->difficulty = trim($request->difficulty);
			$activity->season = trim($request->season);
			$activity->wildlife = trim($request->wildlife);
			$activity->facilities = trim($request->facilities);
			$activity->elevation = trim($request->elevation);
			$activity->public_transportation = trim($request->public_transportation);
			$activity->trail_type = trim($request->trail_type);
				
			$activity->title = 'parent_id=' . $entry->id;
			$activity->parent_id = $entry->id;
			$activity->save();
			
			DB::commit();			

			$request->session()->flash('message.content', 'New Record Successfully Added');
			
			$success = true;
		}
		catch (\Exception $e) 
		{
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());
			
			DB::rollBack();
		}

		if ($success)
			return redirect(route('tour.permalink', [$entry->permalink]));
		else
			return redirect('/tours/indexadmin');
    }

    public function permalink($permalink)
    {
		// get the entry the Laravel way so we can access the gallery photo list
		$entry = Entry::select()
			//->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('permalink', $permalink)
			->first();
			
		if (isset($entry))
		{
			$this->countView($entry);
		}
		
		// get the entry the mysql way so we can have all the main photo and location info
		$entry2 = Entry::getEntry($permalink);		
		
		$id = isset($entry) ? $entry->id : null;
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_PERMALINK, $id);
		
		return $this->handleView($entry, $entry2);
	}
	
    public function view($title, $id)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW, $id);
		
		$entry = Entry::select()
			//->where('site_id', SITE_ID)
			->where('type_flag', ENTRY_TYPE_TOUR)
			->where('deleted_flag', 0)
			->where('id', $id)
			->first();	

		// get the entry the mysql way so we can have all the main photo and location info
		$entry2 = Entry::getEntry($permalink);			
			
		return $this->handleView($entry, $entry2);
	}

    private function handleView($entry, $entry2)
    {
		if (!isset($entry))
		{
			//$request->session()->flash('message.level', 'danger');
			//$request->session()->flash('message.content', 'Entry Not Found');
            return redirect('/tours/index');
		}
		
		$gallery = isset($entry) ? $entry->photos : null;
				
		$activity = Activity::select()
			//->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', $entry->id)
			->first();
					
		$location = array();
		if (isset($entry))
		{
			$locations = DB::table('entries')
				->leftJoin('locations as l1', 'entries.location_id', '=', 'l1.id')
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
				->where('entries.id', $entry->id)
				->first();

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
			if (count($location) != $entry->locations()->count())
			{
				//
				// remove all current locations so they can be replaced
				//
				$entry->locations()->detach();
				
				//
				// save current structure
				//
				$this->saveLocations($entry, $locations);
			}
		}		
				
		$photos = Photo::select()
			->where('site_id', SITE_ID)
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
		
		if (isset($entry2))
		{
			$entry2->description = nl2br($entry2->description);
			$entry2->description = $this->formatLinks($entry2->description);		
		}
		
		$vdata = $this->getViewData([
			'record' => $entry2, 
			'activity' => $activity, 
			'locations' => array_reverse($location), 
			'data' => $this->getViewData(), 
			'photos' => $photos, 
			'page_title' => $entry->title,
			'gallery' => $gallery,
		]);
		
		return view('tours.view', $vdata);
	}
	
    public function viewOrig(Activity $activity)
    {
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $activity->id)
			->orderByRaw('photos.id DESC')
			->get();
					
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
				'activities.map_link', 'activities.map_label', 'activities.map_labelalt',
				'activities.map_link2', 'activities.map_label2', 'activities.map_labelalt2',
				'activities.location_id', 'activities.season', 'activities.wildlife', 
				'activities.public_transportation', 'activities.facilities', 'activities.parking', 'activities.cost', 
				'activities.distance', 'activities.difficulty', 'activities.elevation', 'activities.trail_type'
				)
			->where('type_flag', ENTRY_TYPE_TOUR)
			->where('entries.site_id', SITE_ID)
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
			$entry->title = trim($request->title);
			$entry->permalink = trim($request->permalink);
			$entry->description = trim($request->description);
			$entry->description_short = trim($request->description_short);
			
			//$entry->published_flag = isset($request->published_flag) ? 1 : 0;
			//$entry->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$entry->save();
			
			$activity = Activity::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', '<>', 1)
				->where('parent_id', $entry->id)
				->first();
	
			if (!isset($activity))
			{
				$activity = new Activity();
				$activity->user_id = Auth::id();
				$activity->site_id	 = SITE_ID;
				$activity->parent_id = $entry->id;
				$activity->title = 'parent_id = ' . $entry->id;
			}

			$isDirty = false;

			$activity->map_link = $this->copyDirty($activity->map_link, $request->map_link, $isDirty);
			$activity->map_label = $this->copyDirty($activity->map_label, $request->map_label, $isDirty);
			$activity->map_labelalt = $this->copyDirty($activity->map_lablealt, $request->map_labelalt, $isDirty);
			$activity->map_link2 = $this->copyDirty($activity->map_link2, $request->map_link2, $isDirty);
			$activity->map_label2 = $this->copyDirty($activity->map_label2, $request->map_label2, $isDirty);
			$activity->map_labelalt2 = $this->copyDirty($activity->map_lablealt2, $request->map_labelalt2, $isDirty);

			$activity->info_link = $this->copyDirty($activity->info_link, $request->info_link, $isDirty);
			$activity->cost = $this->copyDirty($activity->cost, $request->cost, $isDirty);
			$activity->parking = $this->copyDirty($activity->parking, $request->parking, $isDirty);
			$activity->distance = $this->copyDirty($activity->distance, $request->distance, $isDirty);
			$activity->difficulty = $this->copyDirty($activity->difficulty, $request->difficulty, $isDirty);
			$activity->season = $this->copyDirty($activity->season, $request->season, $isDirty);
			$activity->wildlife = $this->copyDirty($activity->wildlife, $request->wildlife, $isDirty);
			$activity->facilities = $this->copyDirty($activity->facilities, $request->facilities, $isDirty);
			$activity->elevation = $this->copyDirty($activity->elevation, $request->elevation, $isDirty);
			$activity->public_transportation = $this->copyDirty($activity->public_transportation, $request->public_transportation, $isDirty);
			$activity->trail_type = $this->copyDirty($activity->trail_type, $request->trail_type, $isDirty);
			$activity->activity_type = $this->copyDirty($activity->activity_type, $request->activity_type, $isDirty);
						
			if ($isDirty)
			{
				$activity->save();
			}
						
			return redirect(route('tour.permalink', [$entry->permalink]));
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
			
			$vdata = $this->getViewData([
				'record' => $entry,
			]);
			
			return view('tours.confirmdelete', $vdata);
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
			$activity = Activity::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', '<>', 1)
				->where('parent_id', $entry->id)
				->first();
				
			if (isset($activity))
				$activity->deleteSafe();
			
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $entry->id)
				->get();
			foreach($photos as $photo)
			{
				$redirect = null;
				$message = null;
				$messageLevel = null;
				
				$rc = Controller::deletePhoto($photo, $redirect, $message, $messageLevel);
				
				$request->session()->flash('message.level', $messageLevel);
				$request->session()->flash('message.content', $message);
				
				if (!$rc)
					break;
			}
			
			$entry->deleteSafe();
			
			return redirect('/tours/index');
		}
		
		return redirect('/');
    }
	
    public function location($location_id)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_LOCATION, $location_id);

		$showAll = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true);
		
		$tours = $this->getTourIndexLocation($location_id, /* $allSites = */ true);
							
		$tour_count = isset($tours) ? count($tours) : 0;

		$locations = Location::getPills();

		$vdata = $this->getViewData([
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'locations' => $locations, 
			'showAll' => $showAll, 
			'photo_path' => '/img/entries/', 
			'page_title' => 'Tours, Hikes, Things To Do'
		]);
		
    	return view('tours.index', $vdata);
	}			
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// Privates
	//////////////////////////////////////////////////////////////////////////////////////////
	
}
