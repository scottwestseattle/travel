<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('home');
    }
	
    /**
     * About page
     */
    public function about()
    {
        return view('about');
    }
}
