<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Illuminate\Support\Str;

use Auth;
use Cookie;
use DB;

use App\Event;
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

    public function addTrade(Request $request, Transaction $transaction = null)
    {
		// do it like this because it could be a trade with no lot
		$trade['trade'] = 'buy';
		$trade['lot'] = $transaction;

		// get last account id that was used
        $accountId = Cookie::get('accountId');

		if (isset($transaction) && isset($transaction->symbol))
		{
			$records = DB::table('transactions')
				->where('deleted_flag', 0)
				->where('symbol', $transaction->symbol)
				->get();

			// if there is already more than one trade, then the lot has been sold
			if (isset($records))// && count($records) == 1)
			{
				$trade['lot'] = $records->first();
			}
		}
		else
		{
			// set default account id from cookie
			$trade['accountId'] = $accountId;
		}

		// trade transaction
		return $this->addTransaction($request, $trade);
	}
	
    public function sell(Request $request, Transaction $transaction = null)
    {
		// do it like this because it could be a trade with no lot
		$trade['trade'] = 'sell';
		$trade['lot'] = $transaction;

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
			'tradeType' => $trade['trade'],
			'accountId' => isset($trade['accountId']) ? $trade['accountId'] : null,
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
			$record->buy_price			= $this->trimNull($request->buy_price);
			$record->sell_price			= $this->trimNull($request->sell_price);
			$record->commission			= $this->trimNull($request->commission);
			$record->fees				= $this->trimNull($request->fees);			
			$record->lot_id				= $request->lot_id; // already null for buys
			$record->category_id		= CATEGORY_ID_TRADE;
			
			$fees = floatval($record->commission) + floatval($record->fees);
			
			if ($record->isBuy())
			{
				$record->subcategory_id	= SUBCATEGORY_ID_BUY;
				$action = "Buy";
				$record->shares_unsold = $record->shares;
				
				// compute the trade amount
				$total = (abs(intval($record->shares)) * floatval($record->buy_price)) + $fees;
				$total = -$total; // buys are negative
				
				$record->description = "$action $record->symbol, " . abs($record->shares) . " shares @ \$$record->buy_price";

				if (isset($record->parent_id) && $record->parent_id > 0)
				{
					// save the latest account
					Cookie::queue('accountId', intval($record->parent_id), (30 * 60 * 24)); // 30 days
				}
			}
			else
			{
				$record->subcategory_id	= SUBCATEGORY_ID_SELL;
				$action = "Sell";
				$record->shares = -abs($record->shares);
				$fees = -$fees; // fees come out of the proceeds

				// compute the trade amount
				$total = (abs(intval($record->shares)) * floatval($record->sell_price)) + $fees;
				
				$record->description = "$action $record->symbol, " . abs($record->shares) . " shares @ \$$record->sell_price";
			}
					
			$record->amount = $total;
		}
		else
		{
			$record->description		= $this->trimNull($request->description);
			$record->category_id		= $request->category_id;
			$record->subcategory_id		= $request->subcategory_id;
			$record->amount				= floatval($request->amount);
			
			$record->amount = ($record->isDebit()) ? -$record->amount : $record->amount; // debits are negative
		}

		$v = isset($request->reconciled_flag) ? 1 : 0;
		$record->reconciled_flag = $v;
		
		DB::beginTransaction();
		
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
			else if ($record->isSell())
			{
				//
				// update the corresponding 'buy' trade
				//
				$buy = null;
				$pl = 0.0;
				
				if (isset($record->lot_id))
				{
					$buy = Transaction::select()
						->where('user_id', Auth::id())
						->where('deleted_flag', 0)
						->where('lot_id', $record->lot_id)
						->first();
						
					if (isset($buy))
					{
						// good calculation based on the buy record info
						$pl = abs($record->amount) - abs($buy->amount);
						
						$buy->shares_unsold += $record->shares;
						$buy->sell_price = $record->sell_price;
						$buy->profit = $pl;
						
						$buy->save();
					}
				}

				//
				// add the p/l stock transaction
				//				
				if (isset($buy))
				{
					// already set above
				}
				else
				{
					// rough calculation (that can be manually updated) based on buy price but doesn't include buy commission or fees
					$pl = abs($record->amount) - (abs($record->shares) * abs($record->buy_price));
				}
				
				// add the p/l to the sell transaction
				$record->profit = $pl;
				$record->save();

				// create it even if $pl isn't exact so it can be manually updated
				$this->createTransaction(date('Y-m-d'), $pl, $record->description, $record->parent_id, CATEGORY_ID_INCOME, SUBCATEGORY_ID_STOCKS);
			}			
			
			Event::logAdd(LOG_MODEL, $record->description, $record->amount, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
			
			DB::commit();
		}
		catch (\Exception $e) 
		{
			DB::rollBack(); // not working
			
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->description, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}
			
		$view = isset($record->symbol) ? '/positions/' : '/filter/';

		return redirect($this->getReferer($request, '/' . PREFIX . $view));
	}
	
    private function createTransaction($date, $amount, $description, $accountId, $catId, $subCatId) 
	{ 	
		$record = new Transaction();
				
		$record->user_id 			= Auth::id();	
		$record->transaction_date	= $date;
		$record->amount				= floatval($amount);
		$record->reconciled_flag 	= 1;
		$record->parent_id 			= intval($accountId);		
		$record->subcategory_id		= intval($subCatId);
		$record->category_id		= intval($catId);
		$record->description		= $description;
		$record->type_flag 			= $amount > 0.0 ? TRANSACTION_TYPE_CREDIT : TRANSACTION_TYPE_DEBIT;

		$record->save();

		return true;
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

		$accounts = Controller::getAccounts(LOG_ACTION_ADD, $transaction->isTrade() ? ACCOUNT_TYPE_BROKERAGE : null);		
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
		$record->buy_price = $this->copyDirty($record->buy_price, $request->buy_price, $isDirty, $changes);		
		$record->sell_price = $this->copyDirty($record->sell_price, $request->sell_price, $isDirty, $changes);		
		$record->commission = $this->copyDirty($record->commission, $request->commission, $isDirty, $changes);		
		$record->fees = $this->copyDirty($record->fees, $request->fees, $isDirty, $changes);		
		$record->lot_id = $this->copyDirty($record->lot_id, $request->lot_id, $isDirty, $changes);
		$record->shares_unsold = $this->copyDirty($record->shares_unsold, $request->shares_unsold, $isDirty, $changes);
			
		if ($record->isTrade())
		{
			$fees = floatval($record->commission) + floatval($record->fees);
			
			if ($record->isBuy())
			{
				$record->subcategory_id = SUBCATEGORY_ID_BUY;
				$action = 'Buy';
				
				if (!isset($record->lot_id))
					$record->lot_id = $record->id;
					
				$record->shares = abs($record->shares);
				$amount = (intval($record->shares) * floatval($record->buy_price)) + $fees;
				$request->amount = -$amount;
				$request->description = "$action $record->symbol, " . abs($record->shares) . " shares @ \$$record->buy_price";			
			}
			else
			{
				$record->subcategory_id = SUBCATEGORY_ID_SELL;
				$action = 'Sell';
				$request->amount = (intval($record->shares) * floatval($record->sell_price)) - $fees;
				$record->shares = -abs($record->shares);				
				$request->description = "$action $record->symbol, " . abs($record->shares) . " shares @ \$$record->sell_price";			
			}

		}

		$record->amount = $this->copyDirty(abs($record->amount), floatval($request->amount), $isDirty, $changes);
		
		if ($record->isDebit() && $record->amount > 0) // if it's a debit, make it negative (buys are already set to negative negative above because of negative shares)
			$record->amount = -$record->amount; 
		
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

    public function default(Request $request)
    {
		session(['transactionFilter' => null]); // clear the session so default filter will be used
		
		return $this->filter($request);
	}
	
    public function filter(Request $request)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		
		// decide which filter to use.  all form filters are saved to a session and only cleared HOW???
		if ($request->method() == 'POST')
		{
			// 1. use form request filter and save it to session
			//dump('form request filter');			
			$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
			
			session(['transactionFilter' => $filter]); // save to session for next time
		}
		else
		{
			$sessionFilter = session('transactionFilter');
			if (!isset($sessionFilter))
			{
				// 2. use default filter	
				//dump('default filter');
				$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
			}
			else 
			{
				// 3. use session filter
				//dump('session filter');
				$filter = $sessionFilter;
			}
		}

		$accountId = array_key_exists('account_id', $filter) ? $filter['account_id'] : false;
		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT, $filter['category_id']);
		$records = null;
		$total = 0.0;
		try
		{
			//dump($filter);
			
			// get the records for the filter
			$records = Transaction::getFilter($filter);
		
			// get the total
			$totals = Transaction::getTotal($records, $filter, $accountId);

			//dump($totals);
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
	
    public function trades(Request $request)
    {
		$filter = Controller::getFilter($request);
		$filter['view'] = 'trades';
		
		return $this->showTrades($request, $filter);
	}

    public function positions(Request $request)
    {
    	$filterSessionKey = 'positionsFilter';
		
		if (!$request->isMethod('post'))
		{
			// default to ON
			$filter['showalldates_flag'] = true;			
		}
		
		//
		// decide which filter to use.  all form filters are saved to a session
		//
		if ($request->isMethod('post'))
		{
			// 1. use form request filter and save it to session
			$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);
			
			session([$filterSessionKey => $filter]); // save to session for next time
		}
		else
		{
			$sessionFilter = session($filterSessionKey);
			if (!isset($sessionFilter))
			{
				// 2. use default filter	
				$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);

				// default to ON
				$filter['showalldates_flag'] = true;			
			}
			else 
			{
				// 3. use session filter
				//dump('session filter');
				$filter = $sessionFilter;
			}			
		}
				
		$filter['view'] = 'positions';
		if (isset($filter['symbol']))
		{
			// if one symbol selected
			if (strlen($filter['symbol']) > 1) // all symbols = "0"
			{
				$filter['singleSymbol'] = true;
				$filter['view'] = 'positions-lots';
			}
		}
		
		$filter['quotes'] = true;
		$filter['unsold_flag'] = true;
	
		return $this->showTrades($request, $filter, 'positions');
	}

    public function profit(Request $request)
    {
		$filter = Controller::getFilter($request);
		$filter['sold_flag'] = true;
		$filter['view'] = 'trades';
		
		return $this->showTrades($request, $filter);
	}
	
    public function showTrades(Request $request, $filter)
	{
		if (!$this->isAdmin())
             return redirect('/');

		$accounts = Controller::getAccounts(LOG_ACTION_SELECT, ACCOUNT_TYPE_BROKERAGE);		
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT, CATEGORY_ID_TRADE);
		$symbols = Controller::getSymbols(LOG_ACTION_SELECT);

		$records = null;
		$total = 0.0;

		$records = Transaction::getTrades($filter);
		$totals = Transaction::getTradesTotal($records, $filter);
		array_multisort( array_column($totals['holdings'], "percent"), SORT_DESC, $totals['holdings'] );
		try
		{
			//dump($records);
			//dump($totals);
		}
		catch (\Exception $e) 
		{
			$msg = $e->getMessage();
			//dd($msg);
			
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Trade List', null, $msg);
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
		}

		$vdata = $this->getViewData([
			'records' => $records,
			'totals' => $totals,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'symbols' => $symbols,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
		
		return view(PREFIX . '.' . $filter['view'], $vdata);
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
			$balance = Transaction::getBalanceByDate($filter['account_id'], $filter['to_date'])['balance'];
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
				$totals = Transaction::getTotal($records, $filter, $accountId);
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
    	$id = intval($id);
    	
		if (!$this->isAdmin())
             return redirect('/');

		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);

		switch($query)
		{
			case 'account':
				$filter['account_id'] = $id;
				break;
			case 'account-all':
				$filter['account_id'] = $id;
				$filter['showalldates_flag'] = 1;				
				break;
			case 'category':
				$filter['category_id'] = $id;
				break;
			case 'subcategory':
				$filter['subcategory_id'] = $id;
				break;
			default;
				break;
		}
	
		session(['transactionFilter' => $filter]); // save to session for next time

		//dump($filter);
		
		$accounts = Controller::getAccounts(LOG_ACTION_SELECT);
		$categories = Controller::getCategories(LOG_ACTION_SELECT);
		$subcategories = Controller::getSubcategories(LOG_ACTION_SELECT);
	 
		$records = null;
		$total = 0.0;
		try
		{
			$records = Transaction::getFilter($filter);
			$totals = Transaction::getTotal($records, $filter, $id);
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

		//
		// set up the new date
		//
		$filter = Controller::getFilter($request, /* today = */ true);
		//$filter['from_date'] = $record->transaction_date;
		//$filter['to_date'] = $record->transaction_date;
		
		// move the date up one month
		$date = self::getDateControlSelectedDate($record->transaction_date);
		$date['selected_month'] += 1;
		if ($date['selected_month'] > 12)
		{
			// if it wraps to the next year
			$date['selected_month'] = 1;
			$date['selected_year'] += 1;
		}

		// make sure the day is valid: ex, going from Jan 31st to Feb 28th
		// Note: "strtotime($date . "+ 1 month" doesn't work because it adds 31 days
		$monthDays = cal_days_in_month(CAL_GREGORIAN, $date['selected_month'], $date['selected_year']);
		if ($date['selected_day'] > $monthDays)
			$date['selected_day'] = $monthDays; // set it to last day of the month

		// copy calculated date into the filter
		$filter['selected_day'] = $date['selected_day'];
		$filter['selected_month'] = $date['selected_month'];
		$filter['selected_year'] = $date['selected_year'];

		$vdata = $this->getViewData([
			'record' => $record,
			'accounts' => $accounts,
			'categories' => $categories,
			'subcategories' => $subcategories,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);
		 
		return view(PREFIX . '.copy', $vdata);
	}

    public function expenses(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ true);		
		$expenses = $income = null;
		
		try
		{
			$expenses = Transaction::getExpenses($filter);
			$income = Transaction::getIncome($filter);
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting Expense List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		$vdata = $this->getViewData([
			'dates' => Controller::getDateControlDates(),
			'income' => $income,
			'expenses' => $expenses,
			'filter' => $filter,
		]);
			
		return view(PREFIX . '.expenses', $vdata);
    }	
	
	// this is called by ajax to get the balance for an account during reconcile
    public function getbalance(Request $request, $account_id, $date)
    {
		$date = Controller::trimDate($date);
		$balance = Transaction::getBalanceByDate(intval($account_id), $date)['balance'];
		
		$vdata = $this->getViewDataAjax([
			'balance' => $balance,
		]);		
		
		return view(PREFIX . '.getbalance', $vdata);
	}	
}
