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
    public function index(Request $request)
    {
		$posts = null;

		//
		// set up the site info
		//
		$page_title = config('app.name', 'Travel Guide');
		
		$site = Controller::getSite();
			
		if (isset($site->site_title))
			$page_title .= ' - ' . $site->site_title;
		
		//
		// Set up the sections
		//
		$sections = Controller::getSections();
			
		//
		// get tour info
		//
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
			->where('site_id', SITE_ID)
			->where('parent_id', '=', 0)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id ASC')
			->get();
		
		//
		// get the blogs
		//
		$blogs = Entry::getBlogIndex();

		//
		// get the articles
		//
		$articles = Entry::getEntriesByType(ENTRY_TYPE_ARTICLE);
		
		//
		// save visitor stats
		//
		$this->saveVisitor();
		
		$vdata = [
			'page_title' => $page_title,
			'site' => $site,
			'blogs' => $blogs, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'sliders' => $sliders, 
			'locations' => $locations, 
			'showAll' => $showAll, 
			'photoPath' => $photosWebPath, 
			'sections' => $sections,
			'articles' => $articles,
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
		$events = Event::get(5);

		//
		// get blog entries which need action
		//
		$posts = Entry::getBlogEntriesIndexAdmin(/* $pending = */ true);
		
		//
		// get tours which need more info
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
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->latest()
			->limit(10)
			->get();
			
		$ip = Event::getVisitorIp();
			
		$vdata = $this->getViewData([
			'posts' => $posts,
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors, 
			'ip' => $ip, 
			'new_visitor' => $this->isNewVisitor()
		]);
			
		return view('frontpage.admin', $vdata);
    }
	
    public function posts(Entry $entry)
    {
		$entries = Entry::select()
			->where('site_id', SITE_ID)
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
			->where('site_id', SITE_ID)
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
        return view('frontpage.about');
    }
	
    /**
     * Error page
     */
    public function error()
    {
        return view('frontpage.error');
    }
	
}
