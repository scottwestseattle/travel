<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use DB;
use Auth;
use App\Account;

define('PREFIX', 'accounts');
define('LOG_MODEL', 'accounts');
define('TITLE', 'Account');

class AccountController extends Controller
{	
	public function __construct ()
	{
		$this->prefix = PREFIX;
		$this->title = TITLE;
	}
	
    public function index(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$records = null;
		
		try
		{
			$records = Account::select()
				->where('deleted_flag', 0)
				//->where('account_type_flag', 1)
				//->where('hidden_flag', 0)
				->get();
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . ' List', null, $e->getMessage());

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
			$records = Account::select()
				->where('deleted_flag', 0)
				->get();		
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.indexadmin', $vdata);
    }
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}
		
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           
			//dd($request);
			
		$record = new Account();
		
		$record->user_id = Auth::id();
				
		$record->name				= $this->trimNull($request->name);
		$record->starting_balance	= $this->trimNull($request->starting_balance);
		$record->account_type_flag	= isset($request->account_type_flag) ? $request->account_type_flag : 0;		
		
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->name, $record->site_url, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->name, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
    }

	public function edit(Account $account)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$vdata = $this->getViewData([
			'record' => $account,
		]);		
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Account $account)
    {
		$record = $account;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		
		$record->name = $this->copyDirty($record->name, $request->name, $isDirty, $changes);
		$record->starting_balance = $this->copyDirty($record->starting_balance, $request->starting_balance, $isDirty, $changes);
		
		//dd($request);
		$v = isset($request->account_type_flag) ? intval($request->account_type_flag) : 0;		
		$record->account_type_flag = $this->copyDirty($record->account_type_flag, $v, $isDirty, $changes);

		$v = isset($request->hidden_flag) ? 1 : 0;		
		$record->hidden_flag = $this->copyDirty($record->hidden_flag, $v, $isDirty, $changes);
		
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->name, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'title = ' . $record->name, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}
		else
		{
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'No changes made to ' . $this->title);
		}

		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
	}
	
	public function view(Account $account)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$vdata = $this->getViewData([
			'record' => $account,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }
	
    public function confirmdelete(Account $account)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $account,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Account $account)
    {	
		$record = $account;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->name, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->name, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index');
    }	
	
}
