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
		
		$account->notes = '';
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
		$recordFrom->description		= 'Transfer';
		$recordFrom->notes				= $this->trimNull($request->notes);	
		$recordFrom->category_id		= $category->id;
		$recordFrom->subcategory_id		= $subcategory->id;
		$recordFrom->reconciled_flag 	= 1;
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
			DB::beginTransaction();	
			
			$recordFrom->save();
			$recordTo->save();
			
			// set the transfer_id to the id of the transferFrom transaction record
			$recordFrom->transfer_id = $recordTo->id;
			$recordFrom->save();
			
			// save the transferTo record
			$recordTo->transfer_id	= $recordFrom->id;
			$recordTo->save();
			
			DB::commit();			
			
			Event::logAdd(LOG_MODEL, $recordFrom->description, $recordFrom->amount, $recordFrom->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			DB::rollBack();
			
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'description = ' . $recordFrom->description, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
		
		return redirect('/transactions/filter'); 
	}		
	
	public function edit(Transaction $transaction)
    {		
		if (!$this->isAdmin())
             return redirect('/');
	
		$recordFrom = null;
		$recordTo = null;
		
		// we need to edit the "from" record which has the negative value
		if ($transaction->amount > 0)
		{
			$recordFrom = Transaction::select()
				->where('deleted_flag', 0)
				->where('id', $transaction->transfer_id)
				->first();
				
			$recordTo = $transaction;
		}
		else
		{
			$recordFrom = $transaction;
			
			$recordTo = Transaction::select()
				->where('deleted_flag', 0)
				->where('id', $transaction->transfer_id)
				->first();
		}
		
		$filter = Controller::getDateControlSelectedDate($transaction->transaction_date);		
		$accounts = Controller::getAccounts(LOG_ACTION_ADD);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD);
		
		$recordFrom->amount = abs($recordFrom->amount);
		
		$vdata = $this->getViewData([
			'recordFrom' => $recordFrom,
			'recordTo' => $recordTo,
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

		// put the date together from the mon day year pieces
		$filter = Controller::getFilter($request);
		$date = $this->trimNull($filter['from_date']);
		$amount = abs(floatval($request->amount));
		
		//
		// record from
		//
		$record->amount 			= -$amount;
		$record->transaction_date 	= $filter['from_date'];
		$record->notes 				= trim($request->notes);
		$record->reconciled_flag 	= isset($request->reconciled_flag) ? 1 : 0;
		$record->parent_id 			= $request->parent_id_from;
		$record->transfer_account_id = $request->parent_id_to;
			
		//
		// record to
		//
		$recordTo = Transaction::select()
			->where('deleted_flag', 0)
			->where('id', $transaction->transfer_id)
			->first();

		$recordTo->amount 			= -$record->amount;
		$recordTo->transaction_date = $filter['from_date'];
		$recordTo->notes 			= $record->notes;
		$recordTo->reconciled_flag 	= $record->reconciled_flag;
		$recordTo->parent_id 		= $request->parent_id_to;
		$recordTo->transfer_account_id = $request->parent_id_from;
							
		try
		{
			DB::beginTransaction();	
			
			$record->save();
			$recordTo->save();
			
			DB::commit();			

			Event::logEdit(LOG_MODEL, $record->description, $record->id, $changes);			
				
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been updated');
		}
		catch (\Exception $e) 
		{
			DB::rollBack();
			
			Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'description = ' . $record->description, null, $e->getMessage());
				
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}				

		return redirect($this->getReferer($request, '/transactions/filter/')); 
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
		
		$record2 = Transaction::select()
			->where('deleted_flag', 0)
			->where('id', $transaction->transfer_id)
			->first();
						
		try 
		{
			DB::beginTransaction();	
			
			$record->deleteSafe();
			$record2->deleteSafe();
			
			DB::commit();						
			
			Event::logDelete(LOG_MODEL, $record->description, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			DB::rollBack();
			
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->description, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/transactions/filter/');
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
