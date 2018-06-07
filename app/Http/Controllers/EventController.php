<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class EventController extends Controller
{
	private $prefix = 'events';
	
    public function index()
    {
		if (!$this->isSuperAdmin())
             return redirect('/');

		$records = Event::select()
			->where('deleted_flag', 0)
			->orderByRaw('id DESC')
			->get();
			
		$vdata = [
			'records' => $records
		];
			
		return view($this->prefix . '.index', $vdata);
    }		
}
