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
use App\Reconcile;

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
		// normal, non-trade transaction
		return $this->addTransaction($request);
	}

    public function addTrade(Request $request, $symbol = null)
    {
		// do it like this because it could be a trade with no lot
		$trade['trade'] = true;
		$trade['lot'] = null;
		
		if (isset($symbol))
		{
			$records = DB::table('transactions')
				->where('deleted_flag', 0)
				->where('symbol', $symbol)
				->get();

			/* how to get total shares
			SELECT symbol, sum(shares) as shares FROM `transactions` 
			WHERE 1
			AND symbol = ?
			AND type_flag in (3,4)
			AND lot_id = 7040
			GROUP BY symbol				
			*/
				
			// if there is already more than one trade, then the lot has been sold
			if (isset($records))// && count($records) == 1)
			{
				$trade['lot'] = $records->first();
			}
		}

		// trade transaction
		return $this->addTransaction($request, $trade);
	}
	
    public function addTransaction(Request $request, $trade = null)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$accounts = Controller::getAccounts(LOG_ACTION_ADD, $trade ? ACCOUNT_TYPE_BROKERAGE : null);
		$categories = Controller::getCategories(LOG_ACTION_ADD);
		$subcategories = Controller::getSubcategories(LOG_ACTION_ADD);
				
		$vdata = $this->getViewData([
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
			'trade' => $trade['lot'],
		]);
		
		$view = isset($trade) ? 'add-trade' : 'add';
		
		return view('transactions.' . $view, $vdata);
	}

	public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           			
		$record = new Transaction();
			
		$filter = Controller::getFilter($request);
		
		$record->user_id 			= Auth::id();	
		$record->transaction_date	= $this->trimNull($filter['from_date']);		
		$record->notes				= $this->trimNull($request->notes);
		$record->parent_id			= $request->parent_id;
		
		$v = isset($request->type_flag) ? $request->type_flag : 0;
		$record->type_flag = $v;
		$record->symbol	= $this->trimNull(strtoupper($request->symbol));
		
		if ($record->isTrade())
		{
			$record->shares				= $this->trimNull($request->shares);
			$record->share_price		= $this->trimNull($request->share_price);
			$record->commission			= $this->trimNull($request->commission);
			$record->fees				= $this->trimNull($request->fees);
			$record->lot_id				= $request->lot_id; // already null for buys
			$record->category_id		= CATEGORY_ID_TRADE;
			
			if ($record->isBuy())
			{
				$record->subcategory_id	= SUBCATEGORY_ID_BUY;
				$action = "Buy";
			}
			else
			{
				$record->subcategory_id	= SUBCATEGORY_ID_SELL;
				$action = "Sell";
			}
			
			$record->description = "$action $record->symbol, $record->shares shares @ \$$record->share_price";
			$record->amount	= (intval($record->shares) * floatval($record->share_price)) + floatval($record->commission) + floatval($record->fees);
			$record->amount = $record->isBuy() ? -$record->amount : $record->amount; // if it's a debit, make it negative
		}
		else
		{
			$record->description		= $this->trimNull($request->description);
			$record->category_id		= $request->category_id;
			$record->subcategory_id		= $request->subcategory_id;
			$record->amount				= floatval($request->amount);
			
			$record->amount = ($record->isDebit()) ? -$record->amount : $record->amount; // if it's a debit, make it negative
		}

		$v = isset($request->reconciled_flag) ? 1 : 0;
		$record->reconciled_flag = $v;
		
		try
		{
			if (!isset($record->parent_id) || $record->parent_id <= 0)
				throw new \Exception('Error Adding Trade: Account Not Set');

			$record->save();
			
			// use the new 'buy' record id as the lot id
			if ($record->isBuy())
			{
				$record->lot_id = $record->id;
				$record->save();
			}
			
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
		
		$view = isset($record->symbol) ? '/trades/' : '/filter/';

		return redirect($this->getReferer($request, '/' . PREFIX . $view));
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
		$accounts = Controller::getAccounts(LOG_ACTION_ADD, ACCOUNT_TYPE_BROKERAGE);
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

		$view = $record->isTrade() ? 'edit-trade' : 'edit';
		
		return view(PREFIX . '.' . $view, $vdata);
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
		$record->notes = $this->copyDirty($record->notes, $request->notes, $isDirty, $changes);
		$record->vendor_memo = $this->copyDirty($record->vendor_memo, $request->vendor_memo, $isDirty, $changes);
		$record->parent_id = $this->copyDirty($record->parent_id, $request->parent_id, $isDirty, $changes);
		$record->category_id = $this->copyDirty($record->category_id, $request->category_id, $isDirty, $changes);
		$record->subcategory_id = $this->copyDirty($record->subcategory_id, $request->subcategory_id, $isDirty, $changes);
		
		// set transaction type
		$v = isset($request->type_flag) ? $request->type_flag : 0;
		$record->type_flag = $this->copyDirty($record->type_flag, $v, $isDirty, $changes);

		// trades
		$record->symbol = $this->copyDirty($record->symbol, $request->symbol, $isDirty, $changes);
		$record->shares = $this->copyDirty($record->shares, $request->shares, $isDirty, $changes);
		$record->share_price = $this->copyDirty($record->share_price, $request->share_price, $isDirty, $changes);		
		$record->commission = $this->copyDirty($record->commission, $request->commission, $isDirty, $changes);		
		$record->fees = $this->copyDirty($record->fees, $request->fees, $isDirty, $changes);		
		$record->lot_id = $this->copyDirty($record->lot_id, $request->lot_id, $isDirty, $changes);
				
		if ($record->isTrade())
		{
			$fees = floatval($record->commission) + floatval($record->fees);
			
			if ($record->isBuy())
			{
				$action = 'Buy';
				
				if (!isset($record->lot_id))
					$record->lot_id = $record->id;
			}
			else
			{
				$action = 'Sell';
				$fees = -$fees; // fees and commission come out of the proceeds
			}

			// set the description
			$request->description = "$action $record->symbol, $record->shares shares @ \$$record->share_price";			

			// compute the total amount
			$request->amount = (intval($record->shares) * floatval($record->share_price)) + $fees;
		}

		$record->amount = $this->copyDirty(abs($record->amount), abs(floatval($request->amount)), $isDirty, $changes);
		$record->amount = ($record->isDebit() || $record->isBuy()) ? -$record->amount : $record->amount; // if it's a debit or a buy, make it negative
		
		$record->description = $this->copyDirty($record->description, $request->description, $isDirty, $changes);
						
		$v = isset($request->reconciled_flag) ? 1 : 0;
		$record->reconciled_flag = $this->copyDirty($record->reconciled_flag, $v, $isDirty, $changes);		

		// put the date together from the mon day year pieces
		$filter = Controller::getFilter($request);
		$date = $this->trimNull($filter['from_date']);
		$record->transaction_date = $this->copyDirty($record->transaction_date, $date, $isDirty, $changes);
				
		if ($isDirty)
		{						
			try
			{
				if (!isset($record->parent_id) || $record->parent_id <= 0)
					throw new \Exception('Error Updating Trade: Account Not Set');
					
				//dd($record);
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

		$view = $record->isTrade() ? 'trades' : 'filter';
		return redirect($this->getReferer($request, '/' . PREFIX . '/' . $view . '/')); 
	}
	
    public function inlineupdate(Transaction $transaction, $amount)
    {
		if (!$this->isAdmin())
             return 'Error: not admin';

		$record = $transaction;

		$amount = floatval($amount);
		$record->amount = $amount;
		$msg = 'Updating Transaction Amount to: ' . $amount;
		$rc = '';
		
		try
		{
			$record->save();

			$msg .=  ' -- ' . $rc;
			Event::logEdit(LOG_MODEL, $record->description, $record->id, $msg);			
		}
		catch (\Exception $e) 
		{
			$rc = 'ERROR (check Events)';
			$msg .=  ' -- ' . $rc;
			Event::logException(LOG_MODEL, LOG_ACTION_EDIT, $msg, null, $e->getMessage());		
		}

		return $rc;
	}
	
    public function updateCategory(Request $request, Transaction $transaction, $category_id, $subcategory_id)
    {
		if (!$this->isAdmin())
             return 'Error: not admin';

		$record = $transaction;
		$record->category_id = intval($category_id);
		$record->subcategory_id = intval($subcategory_id);
		$msg = 'Updating Transaction Category to: ' . $record->category_id;
		$rc = '';
		
		try
		{
			$record->save();

			$msg .=  ' -- ' . $rc;
			Event::logEdit(LOG_MODEL, $record->description, $record->id, $msg);			
		}
		catch (\Exception $e) 
		{
			$rc = 'ERROR (check Events)';
			$msg .=  ' -- ' . $rc;
			Event::logException(LOG_MODEL, LOG_ACTION_EDIT, $msg, null, $e->getMessage());		
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
		
    public function filter(Request $request, $showAllDates = false)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		 
		$showAllDates = strtolower(Controller::trimNullStatic($showAllDates, /* alphanum = */ true));
		$showAllDates = ($showAllDates == 'all');

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
		$accountId = false;
		if ($showAllDates || $filter['showalldates_flag'])
		{
			$filter['showalldates_flag'] = true; // in case we're using the command line
			
			// account id is needed to get starting balance to make the totals correct below
			// it is only used when showing all records for a selected account
			$accountId = array_key_exists('account_id', $filter) ? $filter['account_id'] : false;
		}

		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT, $filter['category_id']);
		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getFilter($filter);
			$totals = Transaction::getTotal($records, $accountId);
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
	
    public function trades(Request $request, $showAllDates = true)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		
		$showAllDates = strtolower(Controller::trimNullStatic($showAllDates, /* alphanum = */ true));
		$showAllDates = ($showAllDates == 'all');

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
		$accountId = false;
		if ($showAllDates || $filter['showalldates_flag'])
		{
			$filter['showalldates_flag'] = true; // in case we're using the command line
			
			// account id is needed to get starting balance to make the totals correct below
			// it is only used when showing all records for a selected account
			$accountId = array_key_exists('account_id', $filter) ? $filter['account_id'] : false;
		}

		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT, $filter['category_id']);
		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getTrades($filter);
			$totals = Transaction::getTotal($records, $accountId);
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
							
		return view(PREFIX . '.trades', $vdata);
    }
    
    public function balances(Request $request)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
		$accountId = false;
		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$records = null;
		$total = 0.0;
		try
		{
			$balance = Transaction::getBalanceByDate($filter);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Balance', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());
		
			return redirect('/error');
		}	
						
		$vdata = $this->getViewData([
			'balance' => $balance,
			'accounts' => $accounts,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
							
		return view(PREFIX . '.balances', $vdata);
    }	    
    
    
    public function reconciles(Request $request, $accountId = -1)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
//dd($accounts);
		
		$accountId = intval($accountId);
		
		$records = null;
		$totals = null;
		$total = 0.0;
		$filter = [
			"selected_month" => 12,
			"selected_day" => false,
			"selected_year" => 2018,
			"from_date" => "2018-12-1",
			"to_date" => "2018-12-31",
			"account_id" => $accountId,
			"category_id" => false,
			"subcategory_id" => false,
			"search" => false,
			"unreconciled_flag" => true,
		  	"unmerged_flag" => false,
			"showalldates_flag" => true,
		];

		if ($accountId >= 0)
		{
			try
			{
				$records = Transaction::getFilter($filter);
				$totals = Transaction::getTotal($records, $accountId);
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
		
				return redirect('/error');
			}
		}
					
		return view(PREFIX . '.reconcile', $this->getViewData([
			'records' => $records,
			'totals' => $totals,
			'accounts' => $accounts,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]));
    }	

    public function reconcile(Request $request, Transaction $transaction, $reconcile)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$record = $transaction;

		$record->reconciled_flag = intval($reconcile);
		$msg = 'Transaction has been ' . ($record->reconciled_flag == 1 ? 'Reconciled' : 'Unreconciled');
						
		try
		{
			$record->save();

			Event::logEdit(LOG_MODEL, $record->description, $record->id, $msg);			
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $msg);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_EDIT, $msg, null, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
		
		return back();					    	
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
