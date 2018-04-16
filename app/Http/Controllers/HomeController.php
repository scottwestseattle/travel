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
		
		$entries = Entry::select()
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id')
			->get();
			
		//dd($entries);
		
    	return view('home', compact('entries'));		
    }
	
    public function view(Entry $entry)
    {
		//dd('here');
		return view('entries.view', compact('entry'));
    }
	
    /**
     * About page
     */
    public function about()
    {
        return view('about');
    }
}
