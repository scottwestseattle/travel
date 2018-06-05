<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entry;
use App\Activity;
use App\User;
use App\Photo;
use App\Location;
use App\Visitor;
use DB;

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

		$showAll = $this->getEntryCount();			
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
		
    	return view('frontpage.index', ['posts' => $posts, 'tours' => $tours, 'tour_count' => $tour_count, 'sliders' => $sliders, 
			'locations' => $locations, 'showAll' => $showAll, 'photoPath' => $photosWebPath, 'page_title' => 'Self-guided Tours, Hikes, and Things to do']);
    }

    public function visits()
    {			
		$records = Visitor::select()
			->where('site_id', 1)
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
				->where('site_id', 1)
				->where('deleted_flag', 0)
				->orderByRaw('updated_at DESC')
				->get();
		}
		else
		{
			$records = Visitor::select()
				->where('site_id', 1)
				->where('deleted_flag', 0)
				->latest()
				->get();
		}
		
						
		return view('visits', ['records' => $records]);
    }
	
    public function admin()
    {
		//
		// get records with info missing
		//
		$entries = $this->getTourIndexAdmin(/* $pending = */ true);
			
		// get unconfirmed users
		$users = User::select()
			->where('user_type', '<=', USER_UNCONFIRMED)
			->orderByRaw('id DESC')
			->get();
					
		// get latest visitors
		$visitors = Visitor::select()
			->where('site_id', 1)
			->where('deleted_flag', 0)
			->latest()
			->limit(10)
			->get();
			
		$ip = $this->getVisitorIp();
			
		return view('frontpage.admin', ['records' => $entries, 'users' => $users, 'visitors' => $visitors, 'ip' => $ip, 'new_visitor' => $this->isNewVisitor()]);
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
