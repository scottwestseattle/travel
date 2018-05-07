<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entry;
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
     * Home page
     */
    public function index()
    {
    	//$user = Auth::user(); // original gets current user with all entries
		
		$posts = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();

		$tours = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '=', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		//dd($entries);
		
    	return view('home', compact('posts'), compact('tours'));		
    }
	
    public function view(Entry $entry)
    {
		$photos = $this->getPhotos($entry);
						
		return view('entries.view', ['entry' => $entry, 'data' => $this->getViewData(), 'photos' => $photos]);
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
