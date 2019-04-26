<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App;
use App\Entry;
use App\Event;

class LessonController extends Controller
{
    public function index()
    {		
		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

		$records = $this->getEntriesByType(ENTRY_TYPE_LESSON, /* approved = */ false); // get all because they are displayed by super admin
			
		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'Lessons',
		]);
			
    	return view('lessons.index', $vdata);
    }
    
    public function add(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		return view('entries.add', $this->getViewData([
			'type_flag' => ENTRY_TYPE_LESSON,
			'site_id' => $this->getSiteId(),
			'referer' => '/lessons',
		]));
	}
}
