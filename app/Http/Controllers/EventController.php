<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

define('PREFIX', 'events');
define('LOG_MODEL', 'events');
define('TITLE', 'Event');

class EventController extends Controller
{
	protected $prefix = 'events';
	
    public function index($type_flag = null)
    {
		$type = intval($type_flag);
		
		if (!$this->isSuperAdmin())
             return redirect('/');

		if (isset($type_flag) && $type === 0)
		{
			// show all for any type_flag that isn't a number
			$records = Event::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->orderByRaw('id DESC')
				->get();
		}
		else if ($type > 0)
		{
			// show by type
			$records = Event::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('type_flag', $type)
				->orderByRaw('id DESC')
				->get();
		}
		else
		{
			// default is to show latest 100
			$records = Event::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->orderByRaw('id DESC')
				->limit(100)
				->get();
		}
		
		// get total stats
		$totals['info'] = Event::select()->where('site_id', SITE_ID)->where('deleted_flag', 0)->where('type_flag', LOG_TYPE_INFO)->count();
		$totals['warning'] = Event::select()->where('site_id', SITE_ID)->where('deleted_flag', 0)->where('type_flag', LOG_TYPE_WARNING)->count();
		$totals['error'] = Event::select()->where('site_id', SITE_ID)->where('deleted_flag', 0)->where('type_flag', LOG_TYPE_ERROR)->count();
		$totals['exception'] = Event::select()->where('site_id', SITE_ID)->where('deleted_flag', 0)->where('type_flag', LOG_TYPE_EXCEPTION)->count();
		$totals['other'] = Event::select()->where('site_id', SITE_ID)->where('deleted_flag', 0)->where('type_flag', LOG_TYPE_OTHER)->count();
		$totals['all'] = $totals['info'] + $totals['warning'] + $totals['error'] + $totals['exception'] + $totals['other'];

		return view($this->prefix . '.index', $this->getViewData([
			'records' => $records,
			'totals' => $totals,
		]));
    }	

    public function confirmdelete(Event $event)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $event;

		$vdata = $this->getViewData([
			'record' => $record,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }

    public function deleteAll(Request $request)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$records = Event::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->orderByRaw('id DESC')
			->get();

		foreach($records as $record)
		{		
			try 
			{
				$record->deleteSafe();
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->description, $record->id, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', 'Error deleting all');

				break;
			}			
		}
		
		
		
		return redirect('/' . PREFIX . '/index/');
	}
	
    public function delete(Request $request, Event $event)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $event;

		try 
		{
			$record->deleteSafe();
			//Event::logDelete(LOG_MODEL, $record->title, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Event has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->description, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index/');
    }		
}
