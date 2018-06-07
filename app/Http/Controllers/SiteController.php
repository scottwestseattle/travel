<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use App\Site;
use DB;
use Auth;

define('<input type="hidden" name="referer" value={{$_SERVER["HTTP_REFERER"]}} />', 'TRACK_REFERER');

class SiteController extends Controller
{
	private $prefix = 'sites';
	
    public function index()
    {
		if (!$this->isSuperAdmin())
             return redirect('/');

		$records = Site::select()
			->where('deleted_flag', 0)
			->get();
			
		return view($this->prefix . '.index', ['records' => $records]);
    }	
	
    public function add()
    {
		if (!$this->isSuperAdmin())
             return redirect('/');

		$vdata = [
			'prefix' => $this->prefix,
		];
		 
		return view($this->prefix . '.add', $vdata);
	}
		
    public function create(Request $request)
    {		
		if (!$this->isSuperAdmin())
             return redirect('/');
           
			//dd($request);
			
		$record = new Site();
		
		$record->user_id = Auth::id();
				
		$record->site_name			= $this->trimNull($request->site_name);
		$record->site_url			= $this->trimNull($request->site_url);
		$record->site_title			= $this->trimNull($request->site_title);
		$record->main_section_text	= $this->trimNull($request->main_section_text);
		$record->main_section_subtext = $this->trimNull($request->main_section_subtext);
						
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL_SITES, $record->site_name, $record->site_url, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Site has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_SITES, LOG_ACTION_ADD, 'site_name = ' . $record->site_name, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . $this->prefix . '/index/')); 
    }

	public function edit(Site $site)
    {
		if (!$this->isSuperAdmin())
             return redirect('/');
		 
		$vdata = [
			'prefix' => $this->prefix,
			'record' => $site,
		];
		 
		return view($this->prefix . '.edit', $vdata);
    }
		
    public function update(Request $request, Site $site)
    {
		$record = $site;
		
		if (!$this->isSuperAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		$record->site_name = $this->copyDirty($record->site_name, $request->site_name, $isDirty, $changes);
		$record->site_url = $this->copyDirty($record->site_url, $request->site_url, $isDirty, $changes);
		$record->site_title = $this->copyDirty($record->site_title, $request->site_title, $isDirty, $changes);
		$record->main_section_text = $this->copyDirty($record->main_section_text, $request->main_section_text, $isDirty, $changes);
		$record->main_section_subtext = $this->copyDirty($record->main_section_subtext, $request->main_section_subtext, $isDirty, $changes);
						
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL_SITES, $record->site_name, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Site has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL_SITES, LOG_ACTION_ADD, 'site_name = ' . $record->site_name, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}	

		return redirect($this->getReferer($request, '/' . $this->prefix . '/index/')); 
	}
	
	public function view(Site $site)
    {
		if (!$this->isSuperAdmin())
             return redirect('/');
		 
		$vdata = [
			'prefix' => $this->prefix,
			'record' => $site,
		];
		 
		return view($this->prefix . '.view', $vdata);
    }
	
    public function confirmdelete(Site $site)
    {	
		if (!$this->isSuperAdmin())
             return redirect('/');

		$vdata = [
			'prefix' => $this->prefix,
			'record' => $site,
		];
		 
		return view($this->prefix . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Site $site)
    {	
		$record = $site;
		
		if (!$this->isSuperAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL_SITES, $record->site_name, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Site has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL_SITES, LOG_ACTION_DELETE, $record->site_name, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . $this->prefix . '/index');
    }	
	
}
