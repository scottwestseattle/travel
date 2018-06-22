<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class EventController extends Controller
{
	protected $prefix = 'events';
	
    public function index($type_flag = null)
    {
		$type_flag = intval($type_flag);
		
		if (!$this->isSuperAdmin())
             return redirect('/');

		if ($type_flag > 0)
		{
			$records = Event::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', $type_flag)
				->orderByRaw('id DESC')
				->get();
		}
		else
		{
			$records = Event::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->orderByRaw('id DESC')
				->get();
		}
			
		$vdata = [
			'records' => $records
		];
			
		return view($this->prefix . '.index', $vdata);
    }		
}
