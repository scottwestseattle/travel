<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Account;
use App\Tools;

class Transaction extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

	static public function isTradeStatic($record)
	{
		return (self::isBuyStatic($record) || self::isSellStatic($record));
	}
	
	public function isTrade()
	{
		return ($this->isBuy() || $this->isSell());
	}

	public function isTradeOption()
	{
		return (self::isTradeOptionStatic($this));
	}

	static public function isTradeOptionStatic($record)
	{
		return ($record->type_flag == TRANSACTION_TYPE_BTO_CALL || $record->type_flag == TRANSACTION_TYPE_STC_CALL);
	}

	static public function isBuyStatic($record)
	{
		return ($record->type_flag == TRANSACTION_TYPE_BUY || $record->type_flag == TRANSACTION_TYPE_BTO_CALL);
	}

	static public function isSellStatic($record)
	{
		return ($record->type_flag == TRANSACTION_TYPE_SELL || $record->type_flag == TRANSACTION_TYPE_STC_CALL);
	}
	
	public function isBuy()
	{
		return self::isBuyStatic($this);
	}
	
	public function isSell()
	{
		return self::isSellStatic($this);
	}

	public function isRealTrade()
	{
		return self::isRealTradeStatic($this);
	}

	static public function isRealTradeStatic($record)
	{
		return (!isset($record->trade_type_flag) || $record->trade_type_flag == TRADE_TYPE_REAL);
	}

	public function isDebit()
	{
		return ($this->type_flag == TRANSACTION_TYPE_DEBIT);
	}

	public function isCredit()
	{
		return ($this->type_flag == TRANSACTION_TYPE_CREDIT);
	}
	
    static public function getByVendor($memo)
    {
    	//dump($memo);
    	
		$record = Transaction::select()
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->where('vendor_memo', 'like', $memo . '%')
			->orderByRaw('id DESC')
			->first();

		//dd($record);
		
		return $record;
    }

    static public function get($id)
    {
		$q = '
			SELECT t.*
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory 
				, CONCAT("/img/' . PHOTO_RECEIPT_FOLDER . '/", t.id, "/") as photo_path
			FROM transactions as t
			JOIN accounts ON accounts.id = t.parent_id
			JOIN categories ON categories.id = t.category_id
			JOIN categories as subcategories ON subcategories.id = t.subcategory_id
			LEFT JOIN photos on photos.parent_id = t.id
			WHERE 1=1 
			AND t.user_id = ?
			AND t.deleted_flag = 0
			AND t.id = ? 
		';

		$record = DB::select($q, [Auth::id(), $id]);

		$record = (count($record) > 0) ? $record[0] : null;
		
		return $record;
    }
	
    static public function getIndex($limit = null)
    {
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id
				, trx.vendor_memo, trx.notes
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory, subcategories.id as subcategory_id 
			FROM transactions as trx
			JOIN accounts ON accounts.id = trx.parent_id
			JOIN categories ON categories.id = trx.category_id
			JOIN categories as subcategories ON subcategories.id = trx.subcategory_id
			WHERE 1=1 
			AND trx.user_id = ?
			AND trx.deleted_flag = 0
			ORDER BY trx.transaction_date DESC, trx.id DESC 
		';

		if (isset($limit))
			$q .= ' LIMIT ? ';
		else
			$limit = 100000;
			
		$records = DB::select($q, [Auth::id(), $limit]);

		return $records;
    }
		
    static public function getTotal($records, $filter = null, $accountId = false)
    {
		$total = 0.0;
		$reconciled = 0.0;
		$startingBalance = 0.0;
		$noPhotos = 0;
		$rc = [];
		$allDates = isset($filter) && $filter['showalldates_flag'];
		$monthFlag = !$allDates && isset($filter) && $filter['month_flag'];
		$search = isset($filter) && $filter['search'];

		foreach($records as $record)
		{
			$amount = round(floatval($record->amount), 2);
				
			if (isset($record->photo) && !$record->photo)
				$noPhotos++;
			
			if ($monthFlag && $record->category_id == CATEGORY_ID_TRANSFER)
			{
				// skip the transfers because they jack-up the totals for the statement month view
			}
			else
			{
				if ($record->reconciled_flag == 1)
					$reconciled += $amount;

				$total += $amount;
			}
		}
		
		// this has to be done or else it shows -0 because of a tiny fraction
		$total = round($total, 2);
		$reconciled = round($reconciled, 2);
		
		//dump($filter);
		if ($search)
		{
			// don't add starting balance if we're searching
		}
		else
		{
			if ($allDates)
			{
				// only add starting balance if we're looking at one account and showing all dates
				$startingBalance = $accountId ? Account::getStartingBalance($accountId) : 0.0;
			}
		}

		// if it's just one account, get the balance
		$balance = ($accountId) ? Transaction::getBalanceByDate($accountId) : null;
		$rc['balance'] = $balance['balance'];
		$rc['balance_count'] = $balance['balance_count'];

		$rc['total'] = $total + $startingBalance;
		$rc['no_photos'] = $noPhotos;
		
		if ($total != $reconciled)
		{
			$rc['reconciled'] = $reconciled + $startingBalance;
		}

		return $rc;
    }

    static public function getTradesTotal($records, $filter)
    {
		$total = 0.0;
		$reconciled = 0.0;
		$sharesTrx = $shares = 0;
		$profitTrx = $profit = 0.0;
		$rc = [];
		$holdings = [];
		$profitLoss = isset($filter['profit-loss']) && $filter['profit-loss'];
		$getQuotes = isset($filter['quotes']) && $filter['quotes'];
		
		foreach($records as $record)
		{
			$commission = floatval($record->buy_commission) + floatval($record->sell_commission);
			if (Transaction::isSellStatic($record))
			{
				$sharesTrx = abs(intval($record->shares));
				$shares += $sharesTrx;
				$cost = round((floatval($record->buy_price) * $sharesTrx) + $commission, 2);
			}
			else
			{
				$sharesTrx = intval($record->shares_unsold);
				$shares += $sharesTrx;
				$cost = round((floatval($record->buy_price) * floatval($record->shares_unsold)) + $commission, 2);
			}

			if ($record->reconciled_flag == 1)
				$reconciled += $cost;
			
			$total += $cost;
			
			// only get quotes when requested	
			if ($getQuotes)
			{
				$symbol = $record->symbol;
				
				// only get quotes once per symbol
				if (!array_key_exists($record->symbol, $holdings))
				{
					if (   $record->type_flag == TRANSACTION_TYPE_BUY
						|| $record->type_flag == TRANSACTION_TYPE_SELL)
					{
						$quote = self::getQuote($record->symbol);
						$holdings[$symbol] = $quote;
					}
					else
					{
						// option quotes not available yet
						$holdings['symbol'] = $record->symbol;
						$holdings['nickname'] = $record->symbol;
						$holdings['price'] = 0.0;
						$holdings['change'] = 0.0;
						$holdings['font-size'] = '1.3em';
						$holdings['up'] = false;
					}
					
					$holdings[$symbol]['profit'] = 0.0;
					$holdings[$symbol]['total'] = 0.0;					
					$holdings[$symbol]['shares'] = 0;
					$holdings[$symbol]['dca'] = 0.0;	
					$holdings[$symbol]['lots'] = 0;
				}

				$quote = floatval($holdings[$symbol]['price']);
				$profitTrx = ($quote * abs($record->shares_unsold)) - abs($cost);		
				$profit += $profitTrx;
				
				// add totals for current symbol
				$holdings[$symbol]['profit'] += floatval($profitTrx);
				$holdings[$symbol]['shares'] += $sharesTrx;
				$holdings[$symbol]['total'] += $cost;
				$holdings[$symbol]['lots']++;
			}
			else //if ($profitLoss)
			{
				$symbol = $record->symbol;
				$holdings['symbol'] = $symbol;
				
				// only get quotes once per symbol
				if (!array_key_exists($symbol, $holdings))
				{	
					$holdings[$symbol]['profit'] = 0.0;
					$holdings[$symbol]['total'] = 0.0;					
					$holdings[$symbol]['shares'] = 0;
					$holdings[$symbol]['dca'] = 0.0;	
					$holdings[$symbol]['lots'] = 0;
				}
				
				$profitTrx = ($record->sell_price * abs($record->shares)) - abs($cost);		
				$profit += $profitTrx;
				
				// add totals for current symbol
				$holdings[$symbol]['shares'] += $sharesTrx;
				$holdings[$symbol]['total'] += $cost;
				$holdings[$symbol]['profit'] += floatval($profitTrx);
				$holdings[$symbol]['lots']++;
			}	
			
			$cost = $holdings[$symbol]['total'];
			$pl = $holdings[$symbol]['profit'];
			$plPercent = $cost > 0.0 ? $pl / $cost : 0.0;
			$holdings[$symbol]['plPercent'] = number_format(($plPercent * 100.0), 2);
			$holdings[$symbol]['isProfit'] = $pl >= 0.9;
		}

		// calc the dca
		if ($getQuotes)
		{
			foreach($holdings as $rec)
			{
				$symbol = $rec['symbol'];

				// calc the dca
				$dca = abs($rec['shares'] > 0.0 ? ($rec['total'] / $rec['shares']) : 0.0);
				$holdings[$symbol]['dca'] = $dca; //don't work for 4 digit nbrs: number_format($dca, 4);
			
				// calc the p/l percent
				$percent = floatval($rec['profit']) !== 0.0 ? floatval($rec['profit']) / floatval($rec['total']) : 0.0;
				$holdings[$symbol]['percent'] = number_format($percent, 2);
			}
		}

		//dd($holdings);
		$rc['holdings'] = $holdings;		

		// this has to be done or else it shows -0 because of a tiny fraction
		$total = round($total, 2);
		$reconciled = round($reconciled, 2);
		$profit = round($profit, 2);
		
		$rc['dca'] = abs($shares > 0.0 ? ($total / $shares) : 0.0);
		$rc['total'] = abs($total);
		$rc['shares'] = $shares;
		$rc['profit'] = $profit;
		$rc['profitPercent'] = 
			($rc['profit'] != 0.0 && $rc['total'] != 0.0)
				? ($profit / abs($total) * 100.0)
				: 0.0;
	
		if ($total != $reconciled)
		{
			$rc['reconciled'] = $reconciled;
		}
		
		//dump($rc);
		return $rc;
    }
	
    static public function getFilter($filter)
    {		
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.vendor_memo, trx.notes
				, trx.reconciled_flag, trx.transfer_id, trx.category_id
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory, subcategories.id as subcategory_id 
				, transfer_accounts.name as transfer_account
				, count(photos.id) as photo
			FROM transactions as trx
			JOIN accounts ON accounts.id = trx.parent_id
			LEFT JOIN accounts AS transfer_accounts ON transfer_accounts.id = trx.transfer_account_id 
			JOIN categories ON categories.id = trx.category_id
			JOIN categories AS subcategories ON subcategories.id = trx.subcategory_id
			LEFT JOIN photos ON photos.parent_id = trx.id AND photos.deleted_flag = 0
			WHERE 1=1 
			AND trx.user_id = ?
			AND COALESCE(trx.deleted_flag, 0) = 0
			AND trx.type_flag in (1,2)
		';
		
		if ($filter['showalldates_flag'] == 0) // use date filter
			$q .= ' AND (trx.transaction_date >= STR_TO_DATE(?, "%Y-%m-%d") AND trx.transaction_date <= STR_TO_DATE(?, "%Y-%m-%d")) ';
		
		if ($filter['account_id'] > 0)
			$q .= ' AND trx.parent_id = ' . intval($filter['account_id']) . '';

		if ($filter['category_id'] > 0)
			$q .= ' AND trx.category_id = ' . intval($filter['category_id']) . '';

		if ($filter['subcategory_id'] > 0)
			$q .= ' AND trx.subcategory_id = ' . intval($filter['subcategory_id']) . '';

		if (isset($filter['search']) && strlen($filter['search']) > 0)
		{
			$q .= ' AND ( trx.description like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.vendor_memo like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.notes like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.amount like "%' . $filter['search'] . '%"';
			$q .= '     )';
		}

		if (isset($filter['unreconciled_flag']) && $filter['unreconciled_flag'] == 1)
			$q .= ' AND trx.reconciled_flag = 0 ';		
		
		$q .= '
			GROUP BY trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.vendor_memo, trx.notes
				, trx.reconciled_flag, trx.transfer_id, trx.category_id
				, accounts.name
				, categories.name
				, subcategories.name
				, subcategories.id 
				, transfer_accounts.name
		';
		
		$q .= '
			ORDER BY trx.transaction_date DESC, trx.id DESC 
		';

		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date']]);
	
		return $records;
    }	
	
    static public function getAnnualBalances()
    {
		$q = '
			SELECT YEAR(transaction_date) as year, sum(amount) as balance, count(id) as count
			FROM transactions 
			WHERE 1=1  
			AND user_id = ? 
			AND deleted_flag = 0 
			AND type_flag in (1,2)			
			GROUP BY YEAR(transaction_date)  
		';
					
		$records = DB::select($q, [Auth::id()]);
		
		return $records;
    }

    static public function getMonthlyBalances($limit = PHP_INT_MAX)
    {			
		$q = '
			SELECT DATE_FORMAT(t.transaction_date, "%Y-%M") as month, DATE_FORMAT(t.transaction_date, "%Y-%m") as sortmonth, sum(t.amount) as balance
				, sum(t2.amount) as credit
				, sum(t3.amount) as debit 
			FROM transactions as t
			LEFT JOIN transactions as t2 ON t2.id = t.id AND t2.amount > 0.0 
			LEFT JOIN transactions as t3 ON t3.id = t.id AND t3.amount < 0.0 
			WHERE 1=1  
			AND t.user_id = ? 
			AND t.deleted_flag = 0 
			AND t.category_id <> ?
			AND t.type_flag in (1,2)			
			GROUP BY month, sortmonth
			ORDER BY sortmonth DESC
			LIMIT ? 
		';
			
		$records = DB::select($q, [Auth::id(), CATEGORY_ID_TRANSFER, $limit]);
				
		return $records;
    }
	
    static public function getBalance()
    {
		$balance = 0.0;
		
		$q = '
			SELECT sum(amount) AS balance
			FROM transactions 
			WHERE 1=1  
			AND user_id = ? 
			AND deleted_flag = 0 
			AND type_flag in (1,2)
		';
			
		$records = DB::select($q, [Auth::id()]);

		if (count($records) > 0)
			$balance = floatval($records[0]->balance);
		
		return $balance;
    }

    static public function getBalanceByDate($accountId, $dateTo = null)
    {
		$balance['balance'] = 0.0;
		$balance['balance_count'] = 0;
		$startingBalance = 0.0;
		
		//$q = 'SELECT id, transaction_date, (amount) AS balance ';
		$q = 'SELECT sum(amount) AS balance, count(amount) AS balance_count ';
			
		$q .= ' FROM transactions 
			WHERE 1=1  
			AND user_id = ?
			AND COALESCE(deleted_flag, 0) = 0
			AND parent_id = ?
			AND type_flag in (1,2)
		';
		
		//$q .= ' AND id in (6399, 6372) ';
		
		if (isset($dateTo))
		{
			// not showing ALL DATES
			$q .= ' AND transaction_date <= STR_TO_DATE("' . $dateTo . '", "%Y-%m-%d") ';
		}

		// only add starting balance if we're looking at one account
		$startingBalance = $accountId ? Account::getStartingBalance($accountId) : 0.0;

		$records = DB::select($q, [Auth::id(), intval($accountId)]);
		//dump($records);

		$count = count($records);
		if ($count > 0)
		{
			$balance['balance'] = floatval($records[0]->balance) + $startingBalance;
			$balance['balance_count'] = $records[0]->balance_count;
		}

		return $balance;
    }

    static public function getIncome($filter)
    {
		return self::getIncomeExpenses($filter, true);
	}

    static public function getExpenses($filter)
    {			
		return self::getIncomeExpenses($filter);
	}	
	
    static public function getIncomeExpenses($filter, $income = false)
    {			
		$q = '
			SELECT sum(t.amount) as subtotal, 0 as total, 0 as first
				, s.name as subcategory
				, c.name as category
			FROM transactions t
			JOIN categories as s on s.id = t.subcategory_id
			JOIN categories as c on c.id = s.parent_id
			WHERE 1=1  
			AND t.user_id = ? 
			AND t.deleted_flag = 0 
 			AND (t.transaction_date >= STR_TO_DATE(?, "%Y-%m-%d") AND t.transaction_date <= STR_TO_DATE(?, "%Y-%m-%d")) 
			';
			
		if ($income)
		{
			$q .= ' AND t.category_id = ' . CATEGORY_ID_INCOME;
		}
		else
		{
			$q .= ' AND t.category_id != ' . CATEGORY_ID_INCOME;
		}
					
		$q .= ' 
			AND t.category_id NOT IN (?, ?, ?) 
			GROUP BY subcategory, category 
			ORDER BY c.name ASC 
		;';
			
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date'], CATEGORY_ID_TRANSFER, CATEGORY_ID_TRADE, CATEGORY_ID_DEPOSIT]);

		$totals = [];
		$totals['total'] = 0.0;
		
		// add up the subtotals to get the category total
		foreach($records as $record)
		{
			if (!array_key_exists($record->category, $totals))
			{
				$totals[$record->category] = 0;
				$record->first = 1;
			}
			
			$subtotal = floatval($record->subtotal);
			$totals[$record->category] += $subtotal;
			$totals['total'] += $subtotal;
		}
		
		// put the total on each record for easy access in the view
		foreach($records as $record)
		{
			$record->total = $totals[$record->category];
			$record->grand_total = $totals['total'];
		}
		
		//dump($totals);
		return $records;
    }

    static public function getTrades($filter)
    {
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.notes, trx.reconciled_flag  
				, trx.symbol, trx.shares, trx.buy_price, trx.sell_price, trx.lot_id, trx.shares_unsold
				, trx.buy_commission, trx.sell_commission, trx.trade_type_flag
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory, subcategories.id as subcategory_id 
			FROM transactions as trx
			JOIN accounts ON accounts.id = trx.parent_id
			JOIN categories ON categories.id = trx.category_id
			JOIN categories as subcategories ON subcategories.id = trx.subcategory_id
			WHERE 1=1 
			AND trx.user_id = ?
			AND trx.deleted_flag = 0
			AND trx.type_flag in ( 
			';

		if (isset($filter['typeStocks']) && $filter['typeStocks'] )
			$q .= '' . TRANSACTION_TYPE_BUY . ',' . TRANSACTION_TYPE_SELL . '';
			
		if (isset($filter['typeOptions']) && $filter['typeOptions'] )
			$q .= ', ' . TRANSACTION_TYPE_BTO_CALL . ',' . TRANSACTION_TYPE_STC_CALL . '';

		$q .= ') ';

		if ( $filter['showalldates_flag'] == 0 && isset($filter['from_date']) && isset($filter['to_date']) ) // use date filter
			$q .= ' AND (trx.transaction_date >= STR_TO_DATE(?, "%Y-%m-%d") AND trx.transaction_date <= STR_TO_DATE(?, "%Y-%m-%d")) ';
		
		if (isset($filter['unreconciled_flag']) && $filter['unreconciled_flag'] == 1)
			$q .= ' AND trx.reconciled_flag = 0 ';		
		
		if (isset($filter['sold_flag']) && $filter['sold_flag'] == 1)
		{
			$q .= ' AND (trx.shares_unsold = 0 OR trx.shares_unsold IS NULL OR trx.type_flag = ' . TRANSACTION_TYPE_SELL . ') ';
		}

		if (isset($filter['unsold_flag']) && $filter['unsold_flag'] == 1)
			$q .= ' AND trx.shares_unsold > 0 ';
			
		if ($filter['account_id'] > 0)
			$q .= ' AND trx.parent_id = ' . intval($filter['account_id']) . '';			
			
		if ($filter['subcategory_id'] > 0)
			$q .= ' AND trx.subcategory_id = ' . intval($filter['subcategory_id']) . '';

		if (isset($filter['symbol']) && $filter['symbol'])
			$q .= ' AND trx.symbol = "' . $filter['symbol'] . '"';

		if (isset($filter['search']) && strlen($filter['search']) > 0)
		{
			if (strtolower($filter['search']) == 'paper')
			{
				$q .= ' AND trx.trade_type_flag = ' . intval(TRADE_TYPE_PAPER) . ' ';
			} 
			else
			{
				$q .= ' AND ( trx.amount like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.symbol like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.shares like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.buy_price like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.buy_commission like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.sell_commission like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.notes like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.description like "%' . $filter['search'] . '%"';
				$q .= '       OR trx.lot_id like "%' . $filter['search'] . '%"';
				$q .= '     )';			
			}
		}		
			
		// group by
		//$q .= '
		//	GROUP BY trx.lot_id 
		//';
		
		$q .= '
			ORDER BY trx.transaction_date DESC, trx.id DESC 
		';
		//dd($q);
		
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date']]);
		
		/*
		
SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.notes, trx.reconciled_flag  
, trx.symbol, trx.shares, trx.buy_price, trx.lot_id, trx.shares_unsold 
, accounts.name as account
, categories.name as category
, subcategories.name as subcategory, subcategories.id as subcategory_id 
FROM transactions as trx
JOIN accounts ON accounts.id = trx.parent_id
JOIN categories ON categories.id = trx.category_id
JOIN categories as subcategories ON subcategories.id = trx.subcategory_id
WHERE 1=1 
AND trx.user_id = 1
AND trx.deleted_flag = 0
AND trx.type_flag in (3,4)
AND (trx.transaction_date >= STR_TO_DATE("2021-01-01", "%Y-%m-%d") 
AND  trx.transaction_date <= STR_TO_DATE("2021-12-31", "%Y-%m-%d"))  
AND trx.shares_unsold > 0 
ORDER BY trx.transaction_date DESC, trx.id DESC 		
	
		*/
		//dump($q);
		//dd($records);

		return $records;
    }
	
    static public function getPositions()
    {
		$q = '
			SELECT trx.symbol, sum(trx.shares) as total_shares, count(trx.id) as trades, sum(trx.amount) as pl
				FROM transactions as trx
				JOIN accounts ON accounts.id = trx.parent_id
				JOIN categories ON categories.id = trx.category_id
				JOIN categories as subcategories ON subcategories.id = trx.subcategory_id
				WHERE 1=1 
				AND trx.user_id = ?
				AND trx.deleted_flag = 0
				AND trx.type_flag in (3,4)			
				GROUP BY trx.symbol
				ORDER BY trx.symbol
		';

		$records = DB::select($q, [Auth::id(), TRANSACTION_TYPE_BUY, TRANSACTION_TYPE_SELL]);
		//dd($records);
		
		$positions = [];
		if (isset($records))
		{
			foreach($records as $record)
			{
				if ($record->total_shares > 0)
					$positions[] = $record;
			}
		}
		
		return $positions;
    }	
	
    static public function getSymbolArray(&$error, $unsold = false, $stocksOnly = false)
    {
		// get account list
		$array = [];
		$accountType = isset($accountType) ? $accountType : '%';
		
		try
		{
			$tradeTypes = $stocksOnly ? [TRANSACTION_TYPE_BUY] : [TRANSACTION_TYPE_BUY, TRANSACTION_TYPE_BTO_CALL];
			
			$records = Transaction::select('symbol')
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->whereIn('type_flag', $tradeTypes)
				->where('shares_unsold', $unsold ? '>' : '>=', 0)
				->groupBy('symbol')
				->orderByRaw('symbol')
				->get();
				
			//dd($records);
			
			if (isset($records) && count($records) > 0)
			{
				foreach($records as $record)
				{
					$array[$record->symbol] = $record->symbol;
				}				
			}

			if (count($array) === 0)
				$error .= 'No Symbols found';

		}
		catch (\Exception $e) 
		{
			$msg = $e->getMessage();
			$error .= $msg;
		}			
					
		return $array;
	}

    static public function getQuote($symbol, $nickname = null)
    {
    	//dump('getQuote');
    	
		$quote = null;
		// stock prices
		$url = "https://finance.yahoo.com/quote/$symbol/history";

		//todo: option prices - not implemented yet; no way to specify the expiration date
		$urlOptions = "https://finance.yahoo.com/quote/$symbol/options?p=$symbol&strike=70";
		$page = "";
		try {
			//$page = file_get_contents($url);	
			$page = Tools::file_get_contents_curl($url);
		}
		catch (\Exception $e)
		{
			$msg = $e->getMessage();				
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Stream Error Getting Quotes', null, $msg);		
			request()->session()->flash('message.level', 'danger');
			request()->session()->flash('message.content', 'Unable to get quotes');
			//dd($msg);
		}
		
		$pos = strpos($page, 'qsp-price');
		$text = substr($page, $pos, 750);
		//dd($text);
	
		/*
		"qsp-price" data-field="regularMarketPrice" data-trend="none" data-pricehint="2" 
		value="352.371" active="">352.37</fin-streamer><fin-streamer class="Fw(500) Pstart(8px) Fz(24px)" 
		data-symbol="VOO" data-test="qsp-price-change" data-field="regularMarketChange" data-trend="txt" 
		data-pricehint="2" value="-1.4889832" active=""><span class="C($negativeColor)">-1.49</span>
		</fin-streamer> <fin-streamer class="Fw(500) Pstart(8px) Fz(24px)" data-symbol="VOO" 
		data-field="regularMarketChangePercent" data-trend="txt" data-pricehint="2" data-template="({fmt})" 
		value="-0.0042078313" active=""><span class="C($negativeColor)">(-0.42%)</span>			
		*/
	
		//$pos = strpos($page, '"symbol":"' . $symbol . '"');
		//dump($pos);
		
		//$text = substr($page, $pos);

		// match one or more numbers (with optional ',.+-%() ') between '>' and '<', for example: ">1,920.50<" or ">-1.38 (-0.57%)<"
		preg_match_all('/\>[0-9,.\+\-\%\(\) ]+</', $text, $matches); 
		//dd($matches);

		// "symbol":"^TNX"
		//preg_match_all('/\"symbol\"\:\"' . $symbol . '\"/', $text, $matches); 
		
		// "regularMarketPrice":{"raw":170.14,"fmt":"170.14"},
		//"XLK":{"sourceInterval":15
		//preg_match_all('/\"' . $symbol . '\"\:\{.*\}/', $text, $matches); 
		$results = isset($matches[0][0]) && isset($matches[0][1]);
		$quote = [];
		//dump($matches);
		if ($results)
		{
			$quote['regularMarketPrice'] = trim(trim($matches[0][0], '>'), '<');
			$quote['regularMarketPrice'] = preg_replace("/[^0-9\.\-]/", "", $quote['regularMarketPrice']);
			$quote['regularMarketChangeAmount'] = trim(trim($matches[0][1], '>'), '<');
			
			if (isset($matches[0][3]))
			{
				// fix up the percentage
				$temp = trim(trim($matches[0][3], '>'), '<');
				$temp = trim(trim($temp, '('), ')');
				$temp = trim($temp, '%');
				$quote['regularMarketChangePercent'] = $temp;
			}
		
			//dd($quote);
			if (false) // old way
			{
				$text = substr($matches[0][0], 5, 2000);
				preg_match_all('/\"[a-zA-Z]*\"\:\{[a-zA-Z0-9\"\.\,\:\-\%]*\}/', $text, $matches);
				foreach($matches[0] as $match)
				{
					$parts = explode('":{"', $match);
					if (count($parts) > 1)
					{
						$label = trim($parts[0], '"');
						preg_match_all('/[0-9\-\.]+/', $parts[1], $values);
						$value = (isset($values[0]) && count($values[0]) > 0) ? floatval($values[0][0]) : 0.0;
						if (!array_key_exists($label, $quote))
							$quote[$label] = $value;
					}
				}
			}
		}
	
		// get the price from the quote
		$price = isset($quote['regularMarketPrice']) ? floatval($quote['regularMarketPrice']) : 0.0;
		$change = isset($quote['regularMarketChangeAmount']) ? floatval($quote['regularMarketChangeAmount']) : 0.0;
		$percent = isset($quote['regularMarketChangePercent']) ? floatval($quote['regularMarketChangePercent']) : 0.0;

		// fix up the change
		//$change = (($change > 0.0) ? '+' : '') . number_format($change, 2) . ' ' . number_format($percent, 2) . '%';
		$changeArray['amount'] = number_format($change, 2);
		$changeArray['percent'] = number_format($percent, 2);
		
		// make the quote
		$rc = self::makeQuote($symbol, $nickname, $price, $changeArray);

		return $rc;
	}	
	
	static public function makeQuote($symbol, $nickname, $price, $change)
	{
		$rc['symbol'] = $symbol;
		$rc['nickname'] = $nickname;
		$rc['price'] = floatval($price);
		$rc['change'] = $change;
		$rc['font-size'] = $price < 1000.0 ? '1.3em' : '1.25em';
		$rc['up'] = Tools::startsWith($change['amount'], '-') ? false : true;
		
		return $rc;
	}
}
