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

define('PREFIX', 'transfers');
define('LOG_MODEL', 'transfers');
define('TITLE', 'Transfer');

class TransferController extends Controller
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
	
    public function add(Request $request, Account $account)
    {		
		if (!$this->isAdmin())
             return redirect('/');
		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		
		$vdata = $this->getViewData([
			'record' => $account,
			'accounts' => $accounts,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}	

	public function create(Request $request)
    {		
		if (!$this->isAdmin())
            return redirect('/');
		
		$category = Category::select()
			->whereNull('parent_id')
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->where('name', 'Transfer')
			->first();

		$subcategory = Category::select()
			->whereNotNull('parent_id')
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->where('name', 'Transfer')
			->first();
						
		$filter = Controller::getFilter($request);
		
		// from transaction
		$recordFrom = new Transaction();		
		$recordFrom->transaction_date	= $this->trimNull($filter['from_date']);
		$recordFrom->user_id = Auth::id();	
		$recordFrom->description		= 'Transfer TEST';
		$recordFrom->notes				= $this->trimNull($request->notes);	
		$recordFrom->category_id		= $category->id;
		$recordFrom->subcategory_id		= $subcategory->id;
		$recordFrom->reconciled_flag 	= 0;
		$recordFrom->parent_id	= intval($request->parent_id_from);
		$recordFrom->type_flag = 1;			
		$recordFrom->amount = $this->copyDirty(abs($recordFrom->amount), abs(floatval($request->amount)), $isDirty, $changes);
		$recordFrom->amount = ($recordFrom->type_flag == 1) ? -$recordFrom->amount : $recordFrom->amount; // if it's a debit, make it negative
		$recordFrom->transfer_account_id = intval($request->parent_id_to);
		
		// to transaction
		$recordTo = new Transaction();
		$recordTo->transaction_date	= $recordFrom->transaction_date;
		$recordTo->user_id 			= $recordFrom->user_id;
		$recordTo->description		= $recordFrom->description;
		$recordTo->notes			= $recordFrom->notes;
		$recordTo->category_id		= $recordFrom->category_id;
		$recordTo->subcategory_id	= $recordFrom->subcategory_id;
		$recordTo->reconciled_flag 	= $recordFrom->reconciled_flag;
		$recordTo->parent_id		= $recordFrom->transfer_account_id;
		$recordTo->type_flag 		= 2;
		$recordTo->amount 			= -$recordFrom->amount;
		$recordTo->transfer_account_id = $recordFrom->parent_id;
		
		try
		{
			$recordFrom->save();
			$recordTo->save();
			
			Event::logAdd(LOG_MODEL, $recordFrom->description, $recordFrom->amount, $recordFrom->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $recordFrom->description, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
		
		return redirect('/transactions/filter'); 
	}		
	
	public function edit(Transaction $transaction)
    {
		$record = $transaction;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		$filter = Controller::getDateControlSelectedDate($record->transaction_date);		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD);
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
	
	public function view(Transaction $transaction)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$vdata = $this->getViewData([
			'record' => $transaction,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }
	
    public function confirmdelete(Transaction $transaction)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $transaction,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Transaction $transaction)
    {	
		$record = $transaction;
		
		if (!$this->isAdmin())
             return redirect('/');
		
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
	
    public function copy(Request $request, Transaction $transaction)
    {
		$record = $transaction;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD);
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

}
