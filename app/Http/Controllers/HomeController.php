<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entry;
use App\Activity;
use App\User;
use App\Photo;
use App\Location;
use DB;

define("LONGNAME", "Hike, Bike, Boat");

class HomeController extends Controller
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
     * This is the Home page
     */
    public function index()
    {
		$posts = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();

		$tours = Activity::select()
			->where('approved_flag', '=', 1)
			->where('published_flag', '=', 1)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id DESC')
			->get();

		$locations = Location::select()
			//->leftJoin('locations as l1', 'l1.id', '=', 'locations.parent_id')
			->where('locations.deleted_flag', '=', 0)
			->where('location_type', '>=', LOCATION_TYPE_CITY)
			->where('popular_flag', 1)
			->orderByRaw('locations.location_type ASC')
			->get();

/*			
		$records = DB::table('activities')
			->leftJoin('locations', 'activities.location_id', '=', 'locations.id')
			->select('activities.*', 'locations.name as location_name')
			->get();
*/		
			
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
		// get the sliders
		//
		$sliders = Photo::select()
		->where('parent_id', '=', 0)
		//->whereNull('parent_id')
		->where('deleted_flag', '=', 0)
		->orderByRaw('id ASC')
		->get();
		
		//
		// get visitor stats
		//
		if (!empty($_SERVER["HTTP_CLIENT_IP"]))
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else
		{
		 $ip = $_SERVER["REMOTE_ADDR"];
		}	
		$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);		

		$referrer = '';
		if (array_key_exists("HTTP_REFERER", $_SERVER))
			$referrer = $_SERVER["HTTP_REFERER"];
		
		$entry = new Entry();
		$entry->user_id = 0;
		$entry->title = $ip;
		$entry->description = $host;
		
		if (strlen($referrer) > 0)
			$entry->description_language1 = $referrer;
		
		$entry->is_template_flag = 0;			
		$entry->save();
		
		//dd($sliders);
		
    	return view('home', ['posts' => $posts, 'tours' => $tours, 'sliders' => $sliders, 'locations' => $locations]);
    }

    public function visits()
    {
		$entries = Entry::select()
			->where('user_id', '=', 0)
			->orderByRaw('entries.id DESC')
			->get();
						
		return view('visits', compact('entries'));
    }

    public function admin()
    {
		//
		// get activities pending approval
		//
		// get latest visits
		$activities = DB::table('activities')
			->select()
			->where('published_flag', 0)
			->orWhere('approved_flag', 0)
			->orWhere('location_id', null)
			->orWhere('location_id', 0)
			->orWhere('map_link', null)
			->orderByRaw('updated_at ASC')
			->get();
			
		//dd($activities);

		//
		// get unconfirmed users
		//
		$users = User::select()
			->where('user_type', '<=', USER_UNCONFIRMED)
			->orderByRaw('id DESC')
			->get();
					
		// get latest visits
		$visits = DB::table('entries')
			->select('title', 'description', 'user_id', DB::raw('count(*) as total'))
			->groupBy('title', 'description', 'user_id') // ip address
			->having('user_id', '=', 0)
			->orderByRaw('total DESC')
			->get();
			
		//dd($visits);
			
		return view('admin', ['records' => $activities, 'users' => $users, 'visits' => $visits]);
    }
	
    public function posts(Entry $entry)
    {
		$entries = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('home.posts', compact('entries'));
    }

    public function tours(Entry $entry)
    {
		$entries = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '=', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('home.tours', compact('entries'));
    }
	
    /**
     * About page
     */
    public function about()
    {
        return view('about');
    }
}
