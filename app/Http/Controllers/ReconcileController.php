<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Reconcile;
use App\User;
use App\Event;
use App\Transaction;
use App\Account;
use DateTime;

define('PREFIX', 'reconciles');
define('LOG_MODEL', 'reconcile');
define('TITLE', 'Reconcile');

class ReconcileController extends Controller
{	
	public function __construct ()
	{
		$this->prefix = PREFIX;
		$this->title = TITLE;
		
		parent::__construct();
	}

    public function accounts(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
			
		try
		{
			$records = Account::getIndex(false, /* $reconcileable = */ true);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error getting Account reconcile list', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
		
		$vdata = $this->getViewData([
			'records' => $records,
			'overdueCount' => count(Account::getReconcilesOverdue()),
		]);
			
		return view(PREFIX . '.accounts', $vdata);		
	}	

    public function index(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		// show current month only
		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
				
		$records = null;
		try
		{
			$records = Reconcile::getIndex($filter);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
			'filter' => $filter,
			'dates' => Controller::getDateControlDates(),
		]);
			
		return view(PREFIX . '.index', $vdata);
    }
	
	public function view($id)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$reconcile = Reconcile::get($id);
		
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $id)
			->orderByRaw('created_at ASC')
			->get();
		 
		$vdata = $this->getViewData([
			'record' => $reconcile,
			'photos' => $photos,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }

	// this is add
    public function add(Request $request, Account $account)
    {		
		if (!$this->isAdmin())
             return redirect('/');
						
		// fix up the filter
		$filter = Controller::getFilter($request);
		$filter['account_id'] = $account->id;
		$day = intval($account->reconcile_statement_day);
		$dateBalance = null;
		$dateFilter = null;
		if ($day > 0)
		{
			// use the specified statement day of the current month/year
			$month = intval(date("m"));
			$day = intval($account->reconcile_statement_day);
			$year = intval(date("Y"));
			$dateBalance = '' . $year . '-' . $month . '-' . $day;
			$dateFilter = $dateBalance;
		}
		else
		{
			$month = intval(date("m"));
			$day = intval(date("d"));
			$year = intval(date("Y"));
			$dateFilter = '' . $year . '-' . $month . '-' . $day; // init reconcile date with today
			$dateBalance = null; 								  // use null so all records will be counted in balance
		}
		
		$filter['to_date'] = $dateFilter;
		$filter['selected_month'] = $month;
		$filter['selected_day'] = $day;
		$filter['selected_year'] = $year;
		$dates = Controller::getDateControlDates();

		// get the balance
		$balance = Transaction::getBalanceByDate($filter['account_id'], $dateBalance)['balance'];
						
		$vdata = $this->getViewData([
			'account' => $account,
			'dates' => $dates,
			'balance' => $balance,
			'filter' => $filter,
		]);
				
		return view('reconciles.add', $vdata);
	}

	public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           			
		$filter = Controller::getFilter($request);
					
		$record = new Reconcile();
					
		$record->user_id 			= Auth::id();	
		$record->reconcile_date		= $this->trimNull($filter['from_date']);		
		$record->notes				= $this->trimNull($request->notes);
		$record->account_id			= $request->account_id;
		$record->balance			= floatval($request->balance);
		
		// see if calculated balance was overriden
		$balance = floatval($request->balance_override);
		if ($balance > 0.0)
			$record->balance = $balance;
		
		$record->subtotal_label1 = isset($request->subtotal_label1) ? $request->subtotal_label1 : null;
		$record->subtotal1 = isset($request->subtotal1) ? $request->subtotal1 : null;
		$record->subtotal_label2 = isset($request->subtotal_label2) ? $request->subtotal_label2 : null;
		$record->subtotal2 = isset($request->subtotal2) ? $request->subtotal2 : null;
		$record->subtotal_label3 = isset($request->subtotal_label3) ? $request->subtotal_label3 : null;
		$record->subtotal3 = isset($request->subtotal3) ? $request->subtotal3 : null;
		$record->subtotal_label4 = isset($request->subtotal_label4) ? $request->subtotal_label4 : null;
		$record->subtotal4 = isset($request->subtotal4) ? $request->subtotal4 : null;
		$record->subtotal_label5 = isset($request->subtotal_label5) ? $request->subtotal_label5 : null;
		$record->subtotal5 = isset($request->subtotal5) ? $request->subtotal5 : null;

		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->notes, $record->balance, $record->id);
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Reconcile record has been added');
			
			DB::commit();
		}
		catch (\Exception $e) 
		{			
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->description, null, $e->getMessage());
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
			
		return redirect('/reconciles/account/' . $request->account_id);
	}
	
    private function createReconcile($date, $balance, $notes, $accountId) 
	{ 	
		$record = new Reconcile();
				
		$record->user_id 			= Auth::id();	
		$record->reconcile_date		= $date;
		$record->balance			= floatval($balance);
		$record->account_id			= intval($accountId);
		$record->notes				= $notes;

		$record->save();

		return true;
    }	
	
	public function edit(Reconcile $reconcile)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $reconcile;
		$filter = Controller::getDateControlSelectedDate($record->reconcile_date);	
		
		$vdata = $this->getViewData([
			'record' => $record,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
		
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Reconcile $reconcile)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $reconcile;
				 
		$isDirty = false;
		$changes = '';

		$record->balance = $this->copyDirty($record->balance, $request->balance, $isDirty, $changes);
		$record->notes = $this->copyDirty($record->notes, $request->notes, $isDirty, $changes);
				
		// put the date together from the mon day year pieces
		$filter = Controller::getFilter($request);
		$date = $this->trimNull($filter['from_date']);
		$record->reconcile_date = $this->copyDirty($record->reconcile_date, $date, $isDirty, $changes);

		$record->subtotal_label1 = $this->copyDirty($record->subtotal_label1, $request->subtotal_label1, $isDirty, $changes);
		$record->subtotal1 = $this->copyDirty($record->subtotal1, $request->subtotal1, $isDirty, $changes);
		$record->subtotal_label2 = $this->copyDirty($record->subtotal_label2, $request->subtotal_label2, $isDirty, $changes);
		$record->subtotal2 = $this->copyDirty($record->subtotal2, $request->subtotal2, $isDirty, $changes);
		$record->subtotal_label3 = $this->copyDirty($record->subtotal_label3, $request->subtotal_label3, $isDirty, $changes);
		$record->subtotal3 = $this->copyDirty($record->subtotal3, $request->subtotal3, $isDirty, $changes);
		$record->subtotal_label4 = $this->copyDirty($record->subtotal_label4, $request->subtotal_label4, $isDirty, $changes);
		$record->subtotal4 = $this->copyDirty($record->subtotal4, $request->subtotal4, $isDirty, $changes);
		$record->subtotal_label5 = $this->copyDirty($record->subtotal_label5, $request->subtotal_label5, $isDirty, $changes);
		$record->subtotal5 = $this->copyDirty($record->subtotal5, $request->subtotal5, $isDirty, $changes);				
				
		if ($isDirty)
		{						
			try
			{
				//dd($record);
				$record->save();

				Event::logEdit(LOG_MODEL, $record->account->name, $record->id, $changes);			
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Reconcile record has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'Reconcile account: ' . $record->account->name, null, $e->getMessage());
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}
		else
		{
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'No changes made to reconcile record');
		}

		return redirect('/' . PREFIX . '/index');
	}
	

    public function confirmdelete(Reconcile $reconcile)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $reconcile;
			 
		return view(PREFIX . '.confirmdelete', $this->getViewData([
			'record' => $record,
		]));
    }
	
    public function delete(Request $request, Reconcile $reconcile)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $reconcile;
		 				
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->description, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->description, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX);
    }	

}
