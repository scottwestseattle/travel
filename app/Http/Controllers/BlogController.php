<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Entry;

class BlogController extends Controller
{
	private $prefix = 'blogs';

    public function index()
    {				
		$records = Entry::getEntriesByType(ENTRY_TYPE_BLOG);

		$vdata = [
			'records' => $records,
			'redirect' => '/' . $this->prefix . '/index'
		];

    	return view($this->prefix . '.index', $vdata);
    }

    public function indexadmin()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$records = Entry::getEntriesByType(ENTRY_TYPE_BLOG, /* approved = */ false);

		$vdata = [
			'records' => $records,
			'redirect' => '/' . $this->prefix . '/indexadmin'
		];
		
    	return view($this->prefix . '.indexadmin', $vdata);
    }
	
    public function show($id)
    {
		$record = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('id', $id)
			->where('type_flag', ENTRY_TYPE_BLOG)
			->first();

		$records = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('parent_id', $id)
			->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
			->orderByRaw('display_date ASC')
			->get();
			
		$vdata = [
			'record' => $record, 
			'records' => $records, 
		];
		
		return view($this->prefix . '.view', $vdata);
	}
}
