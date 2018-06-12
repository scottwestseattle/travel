<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use DB;
use Auth;
use App\Template;

define('PREFIX', 'templates');
define('LOG_MODEL', 'templates');

class TemplateController extends Controller
{
	private $prefix = 'templates';
	
    public function index(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$records = null;
		
		try
		{
			$records = Template::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Template List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.index', $vdata);
    }	

    public function indexadmin(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
		
		try
		{
			$records = Template::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->get();		
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Template List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.indexadmin', $vdata);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);
		
		$record = Template::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('published_flag', 1)
			->where('approved_flag', 1)
			->where('permalink', $permalink)
			->first();
						
		if (!isset($record))
		{
			$msg = 'Permalink Entry Not Found: ' . $permalink;
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL, LOG_ACTION_VIEW, /* title = */ $msg);			
			
            return redirect('/' . PREFIX . '/index');
		}
			
		$vdata = $this->getViewData([
			'record' => $record, 
		]);
		
		return view(PREFIX . '.view', $vdata);
	}
		
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'prefix' => PREFIX,
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}
		
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           
			//dd($request);
			
		$record = new Template();
		
		$record->site_id = SITE_ID;
		$record->user_id = Auth::id();
				
		$record->title					= $this->trimNull($request->title);
		$record->description			= $this->trimNull($request->description);

		$record->permalink = $this->trimNull($request->permalink);
		if (!isset($record->permalink))
			$record->permalink = $this->createPermalink($record->title);
		
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->title, $record->site_url, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Template has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->title, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
    }

	public function edit(Template $template)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$vdata = $this->getViewData([
			'prefix' => PREFIX,
			'record' => $template,
		]);		
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Template $template)
    {
		$record = $template;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		
		$record->title = $this->copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = $this->copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->permalink = $this->copyDirty($record->permalink, $request->permalink, $isDirty, $changes);
		
		$record->published_flag = 0;
		$record->approved_flag = 0;
						
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->title, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Template has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'title = ' . $record->title, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}	

		return redirect($this->getReferer($request, '/' . PREFIX . '/index/')); 
	}
	
	public function view(Template $template)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$vdata = $this->getViewData([
			'prefix' => PREFIX,
			'record' => $template,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }
	
    public function confirmdelete(Template $template)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'prefix' => PREFIX,
			'record' => $template,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Template $template)
    {	
		$record = $template;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->title, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Template has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->title, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index');
    }	

    public function publish(Request $request, Template $template)
    {	
    	if (!$this->isOwnerOrAdmin($template->user_id))
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $template,
		]);
		
		return view('templates.publish', $vdata);
    }
	
    public function publishupdate(Request $request, Template $template)
    {	
		$record = $template; 
		
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($record->user_id))
        {			
			$published = isset($request->published_flag) ? 1 : 0;
			$record->published_flag = $published;
			
			if ($published === 0) // if it goes back to private, then it has to be approved again
				$record->approved_flag = 0;
			else
				$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			$record->save();
			
			//return redirect(route(PREFIX . '.permalink', [$record->permalink]));
			return redirect('/' . PREFIX . '/indexadmin');
		}
		else
		{
			return redirect('/');
		}
    }	
}
