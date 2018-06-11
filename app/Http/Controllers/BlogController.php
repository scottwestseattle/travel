<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Entry;
use App\Photo;
use App\Event;

class BlogController extends Controller
{
	private $prefix = 'blogs';

    public function addpost(Request $request, $id)
    {
		$idsave = $id;
		$id = intval($id);
		
		if (!$this->isAdmin())
			return redirect('/');
		
		$record = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('id', $id)
			->where('type_flag', ENTRY_TYPE_BLOG)
			->first();
			
		if (!isset($record))
		{
			$msg = 'Add Post: Blog Not Found For ID: ' . $idsave;
			Event::logError(LOG_MODEL_BLOGS, LOG_ACTION_ADD, /* title = */ $msg, null, /* record_id = */ $id);
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
            return redirect('/blogs/indexadmin');
		}
	
		$vdata = $this->getViewData([
			'title' => $record->title,
			'type_flag' => ENTRY_TYPE_BLOG_ENTRY,
			'parent_id' => $record->id,
		]);
		
		return view('entries.add', $vdata);							
	}
	
    public function index()
    {		
		$records = Entry::getBlogIndex();
		
		$vdata = $this->getViewData([
			'records' => $records,
			'redirect' => '/' . $this->prefix . '/index',
		]);

    	return view($this->prefix . '.index', $vdata);
    }

    public function indexadmin()
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$records = Entry::getEntriesByType(ENTRY_TYPE_BLOG, /* approved = */ false);

		$vdata = $this->getViewData([
			'records' => $records,
			'redirect' => '/' . $this->prefix . '/indexadmin'
		]);
		
    	return view($this->prefix . '.indexadmin', $vdata);
    }
	
    public function show(Request $request, $id)
    {
		$id = intval($id);
		
		try
		{
			$record = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('id', $id)
				->where('type_flag', ENTRY_TYPE_BLOG)
				->first();
				
			if (!isset($record))
			{
				$msg = "Error Viewing Blog ID: $id, record not found";
			
				Event::logError(LOG_MODEL_BLOGS, LOG_ACTION_VIEW, $msg);
					
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
				
				return redirect('/error');
			}				

			$records = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('parent_id', $id)
				->where('type_flag', ENTRY_TYPE_BLOG_ENTRY)
				->orderByRaw('display_date ASC')
				->get();
				
			$photos = Photo::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', '<>', 1)
				->where('parent_id', '=', $record->id)
				->orderByRaw('created_at ASC')
				->get();		
				
			$vdata = $this->getViewData([
				'record' => $record, 
				'records' => $records, 
				'photos' => $photos,
			]);
				
			return view($this->prefix . '.view', $vdata);				
		}
		catch (\Exception $e) 
		{
			$msg = 'Error Viewing Blog ID ' . $id;
			Event::logException(LOG_MODEL_BLOGS, LOG_ACTION_VIEW, $msg, null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg . ': ' . $e->getMessage());		
			
			return redirect('/error');			
		}								
		
	}
}
