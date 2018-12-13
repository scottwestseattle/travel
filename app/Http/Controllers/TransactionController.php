<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use DB;
use Auth;
use App\Transaction;
use App\Account;
use App\Category;
use DateTime;
use App\Photo;

define('PREFIX', 'transactions');
define('LOG_MODEL', 'transactions');
define('TITLE', 'Transaction');
define('TRX_LIMIT', 10);

class TransactionController extends Controller
{	
	public function __construct ()
	{
		$this->prefix = PREFIX;
		$this->title = TITLE;
		
		parent::__construct();
	}
	
    public function summary(Request $request, $showAll = null)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$balance = null;
		$monthlyBalances = null;
		$annualBalances = null;
			
		try
		{
			$balance = Transaction::getBalance();
			$annualBalances = Transaction::getAnnualBalances();
			$monthlyBalances = Transaction::getMonthlyBalances(isset($showAll) ? PHP_INT_MAX : 12);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . ' List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		$vdata = $this->getViewData([
			'balance' => $balance,
			'monthlyBalances' => $monthlyBalances,
			'annualBalances' => $annualBalances,
		]);
			
		return view(PREFIX . '.summary', $vdata);
    }	

    public function index(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getIndex();
			$total = Transaction::getTotal($records);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
			'total' => $total,
		]);
			
		return view(PREFIX . '.index', $vdata);
    }
	
    public function add(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD);
				
		$vdata = $this->getViewData([
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}

	public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           			
		$record = new Transaction();
		
		$filter = Controller::getFilter($request);
		$record->transaction_date	= $this->trimNull($filter['from_date']);
		
		$record->user_id = Auth::id();	
		$record->description		= $this->trimNull($request->description);
		$record->notes				= $this->trimNull($request->notes);
		$record->parent_id			= $request->parent_id;
		$record->category_id		= $request->category_id;
		$record->subcategory_id		= $request->subcategory_id;
		$record->amount				= floatval($request->amount);

		$v = isset($request->type_flag) ? $request->type_flag : 0;
		$record->type_flag = $v;
		
		$v = isset($request->reconciled_flag) ? 1 : 0;
		$record->reconciled_flag = $v;
			
		$record->amount = $this->copyDirty(abs($record->amount), abs(floatval($request->amount)), $isDirty, $changes);
		$record->amount = ($record->type_flag == 1) ? -$record->amount : $record->amount; // if it's a debit, make it negative
		
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->description, $record->amount, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->description, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
		
		return redirect($this->getReferer($request, '/' . PREFIX . '/filter/')); 		
	}
	
	public function edit(Transaction $transaction)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;
		
		// if it's a transfer record, let the transfer controller handle it
		if (isset($record->transfer_id))
			return redirect('/transfers/edit/' . $record->id); 
		
		if (!$this->isAdmin())
             return redirect('/');
		
		$filter = Controller::getDateControlSelectedDate($record->transaction_date);		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD, $record->category_id);
		$transaction->amount = abs($transaction->amount);
		
		$vdata = $this->getViewData([
			'record' => $record,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Transaction $transaction)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
				
		$record->user_id = Auth::id();	
		$record->description = $this->copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->notes = $this->copyDirty($record->notes, $request->notes, $isDirty, $changes);
		$record->vendor_memo = $this->copyDirty($record->vendor_memo, $request->vendor_memo, $isDirty, $changes);
		$record->parent_id = $this->copyDirty($record->parent_id, $request->parent_id, $isDirty, $changes);
		$record->category_id = $this->copyDirty($record->category_id, $request->category_id, $isDirty, $changes);
		$record->subcategory_id = $this->copyDirty($record->subcategory_id, $request->subcategory_id, $isDirty, $changes);		
		
		$v = isset($request->type_flag) ? $request->type_flag : 0;
		$record->type_flag = $this->copyDirty($record->type_flag, $v, $isDirty, $changes);
				
		$v = isset($request->reconciled_flag) ? 1 : 0;
		$record->reconciled_flag = $this->copyDirty($record->reconciled_flag, $v, $isDirty, $changes);		

		// put the date together from the mon day year pieces
		$filter = Controller::getFilter($request);
		$date = $this->trimNull($filter['from_date']);
		$record->transaction_date = $this->copyDirty($record->transaction_date, $date, $isDirty, $changes);
		
		$record->amount = $this->copyDirty(abs($record->amount), abs(floatval($request->amount)), $isDirty, $changes);
		$record->amount = ($record->type_flag == 1) ? -$record->amount : $record->amount; // if it's a debit, make it negative
					
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->description, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'description = ' . $record->description, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}
		else
		{
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'No changes made to ' . $this->title);
		}

		return redirect($this->getReferer($request, '/' . PREFIX . '/filter/')); 
	}
	
	public function view($id)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$transaction = Transaction::get($id);
		
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $id)
			->orderByRaw('created_at ASC')
			->get();
		 
		$vdata = $this->getViewData([
			'record' => $transaction,
			'photos' => $photos,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }
	
    public function confirmdelete(Transaction $transaction)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;
		 
		// if it's a transfer record, let the transfer controller handle it
		if (isset($record->transfer_id))
			return redirect('/transfers/confirmdelete/' . $record->id); 
		 
		$vdata = $this->getViewData([
			'record' => $transaction,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Transaction $transaction)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;
		 				
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
			
		return redirect('/' . PREFIX . '/filter/');
    }	
		
    public function filter(Request $request)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		 
		//todo: session(['account_id' => null]);

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);	
		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT);
	 
		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getFilter($filter);
			$totals = Transaction::getTotal($records);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());
		
			return redirect('/error');
		}	
						
		$vdata = $this->getViewData([
			'records' => $records,
			'totals' => $totals,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,			
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
							
		return view(PREFIX . '.filter', $vdata);
    }	

    public function show(Request $request, $query, $id)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);	

/*
		$filter = [
		  "selected_month" => 12,
		  "selected_day" => false,
		  "selected_year" => 2018,
		  "from_date" => "2018-12-1",
		  "to_date" => "2018-12-31",
		  "account_id" => session('account_id', false),
		  "category_id" => false,
		  "subcategory_id" => false,
		  "search" => false,
		  "unreconciled_flag" => false,
		  "unmerged_flag" => false,
		];
*/

		switch($query)
		{
			case 'account':
				$filter['account_id'] = $id;
				//session(['account_id' => $id]);
				break;
			case 'category':
				$filter['category_id'] = $id;
				//session(['category_id' => $id]);
				break;
			case 'subcategory':
				$filter['subcategory_id'] = $id;
				//session(['subcategory_id' => $id]);
				break;
			default;
				break;
		}
	
		//dump($filter);
		
		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT);
	 
		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getFilter($filter);
			$totals = Transaction::getTotal($records);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());
		
			return redirect('/error');
		}	
						
		$vdata = $this->getViewData([
			'records' => $records,
			'totals' => $totals,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,			
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
							
		return view(PREFIX . '.filter', $vdata);
    }	
	
    public function copy(Request $request, Transaction $transaction)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD, $record->category_id);
		$transaction->amount = abs($transaction->amount);
		
		$vdata = $this->getViewData([
			'record' => $record,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
		]);
		 
		return view(PREFIX . '.copy', $vdata);
	}

    public function expenses(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);		
		$records = null;
			
		try
		{
			$records = Transaction::getExpenses($filter);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Expense List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		$vdata = $this->getViewData([
			'dates' => Controller::getDateControlDates(),
			'records' => $records,
			'filter' => $filter,
		]);
			
		return view(PREFIX . '.expenses', $vdata);
    }	
	
}
