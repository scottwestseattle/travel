<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Entry;
use App\Activity;
use App\User;
use App\Photo;
use App\Location;
use App\Visitor;
use App\Site;
use App\Event;

define("LONGNAME", "Hike, Bike, Boat");

class FrontPageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * This is the front page
     */
    public function index()
    {
		$posts = null;

		// set up the site info
		$welcome = 'Welcome to ' . config('app.name', 'Travel');
		$welcome2 = 'Travel is our Business!!';
		$page_title = 'Travel Information';
		$page_title = config('app.name', 'Travel Guide');
		
		$site = Site::select()->first();
		if (isset($site))
		{
			if (isset($site->main_section_text))
				$welcome = $site->main_section_text;
			
			if (isset($site->main_section_subtext))
				$welcome2 = $site->main_section_subtext;
			
			if (isset($site->site_title))
				$page_title .= ' - ' . $site->site_title;
		}
		
		//dd($site);
		
		$showAll = $this->getEntryCount(ENTRY_TYPE_TOUR);			
		$tours = $this->getTourIndex(/* approved = */ true);
		//dd($tours);
		$tour_count = isset($tours) ? count($tours) : 0;

		$locations = Location::getPills();
		//foreach($locations as $location)
			//dd($location);

		//
		// get tour page link and main photo
		//
		$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
		$photosWebPath = '/public/img/entries/';
		
		//
		// get the sliders
		//
		$sliders = Photo::select()
		->where('parent_id', '=', 0)
		//->whereNull('parent_id')
		->where('deleted_flag', '=', 0)
		->orderByRaw('id ASC')
		->get();
		
		//
		// save visitor stats
		//
		$this->saveVisitor();
		
		$vdata = [
			'welcome' => $welcome,
			'welcome2' => $welcome2,
			'page_title' => $page_title,
			'site' => $site,
			'posts' => $posts, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'sliders' => $sliders, 
			'locations' => $locations, 
			'showAll' => $showAll, 
			'photoPath' => $photosWebPath, 
		];
		
    	return view('frontpage.index', $vdata);
    }

    public function visits()
    {			
		$records = Visitor::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->latest()
			->get();
						
		return view('visits', ['records' => $records]);
    }

    public function visitors($sort = null)
    {			
		if (isset($sort))
		{
			$records = Visitor::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->orderByRaw('updated_at DESC')
				->get();
		}
		else
		{
			$records = Visitor::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->latest()
				->get();
		}
		
						
		return view('visits', ['records' => $records]);
    }
	
    public function admin()
    {
		//
		// get latest events
		//
		$events = Event::get(10);
		
		//
		// get records with info missing
		//
		$entries = $this->getTourIndexAdmin(/* $pending = */ true);
			
		//
		// get unconfirmed users
		//
		$users = User::select()
			->where('user_type', '<=', USER_UNCONFIRMED)
			->orderByRaw('id DESC')
			->get();
					
		//
		// get latest visitors
		//
		$visitors = Visitor::select()
			->where('site_id', 1)
			->where('deleted_flag', 0)
			->latest()
			->limit(10)
			->get();
			
		$ip = $this->getVisitorIp();
			
		$vdata = [
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors, 
			'ip' => $ip, 
			'new_visitor' => $this->isNewVisitor()
		];
			
		return view('frontpage.admin', $vdata);
    }
	
    public function posts(Entry $entry)
    {
		$entries = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('frontpage.posts', compact('entries'));
    }

    public function tours(Entry $entry)
    {
		$entries = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '=', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('frontpage.tours', compact('entries'));
    }
	
    /**
     * About page
     */
    public function about()
    {
        return view('about');
    }
}
