<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Account;

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

	static public function isBuyStatic($record)
	{
		return ($record->type_flag == TRANSACTION_TYPE_BUY);
	}

	static public function isSellStatic($record)
	{
		return ($record->type_flag == TRANSACTION_TYPE_SELL);
	}
	
	public function isBuy()
	{
		return ($this->type_flag == TRANSACTION_TYPE_BUY);
	}
	
	public function isSell()
	{
		return ($this->type_flag == TRANSACTION_TYPE_SELL);
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
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.vendor_memo, trx.notes
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
		$monthFlag = isset($filter) && $filter['month_flag'];
		$allDates = isset($filter) && $filter['showalldates_flag'];
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
		$shares = 0;
		$rc = [];

		foreach($records as $record)
		{
			$amount = round(floatval($record->amount), 2);

			if ($record->reconciled_flag == 1)
				$reconciled += $amount;
			
			$total += $amount;
			$shares += intval($record->shares);
			
			// only get quotes when requested AND once per symbol
			if ($filter['quotes'] && !array_key_exists($record->symbol, $rc))
			{
				$rc[$record->symbol] = self::getQuote($record->symbol);
			}
		}
		
		// this has to be done or else it shows -0 because of a tiny fraction
		$total = round($total, 2);
		$reconciled = round($reconciled, 2);
		
		$rc['total'] = $total;
		$rc['shares'] = $shares;
		
		if ($total != $reconciled)
		{
			$rc['reconciled'] = $reconciled;
		}

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
			AND trx.deleted_flag = 0
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

    static public function getBalanceByDate($filter)
    {
		$balance = 0.0;
		
		$q = '
			SELECT sum(amount) AS balance
			FROM transactions 
			WHERE 1=1  
			AND user_id = ? 
			AND deleted_flag = 0
			AND reconciled_flag = 1
			AND transaction_date <= STR_TO_DATE(?, "%Y-%m-%d") 
			AND parent_id = ?
			AND type_flag in (1,2)
		';
		
/*
SELECT sum(amount) FROM `transactions` 
where 1=1 
AND parent_id = 31 
and transaction_date < STR_TO_DATE("2019-10-13", "%Y-%m-%d") 
AND deleted_flag = 0 
AND reconciled_flag = 1
*/
			
		$records = DB::select($q, [Auth::id(), $filter['to_date'], $filter['account_id']]);
		if (count($records) > 0)
			$balance = floatval($records[0]->balance);
		
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
			$q .= ' AND t.category_id = ' . CATEGORY_ID_INCOME;
		else
			$q .= ' AND t.category_id != ' . CATEGORY_ID_INCOME;
					
		$q .= ' 
			AND t.category_id NOT IN (?, ?) 
			GROUP BY subcategory, category 
			ORDER BY c.name ASC 
		;';
			
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date'], CATEGORY_ID_TRANSFER, CATEGORY_ID_TRADE]);

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
				, trx.symbol, trx.shares, trx.buy_price, trx.lot_id, trx.shares_unsold 
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
			AND trx.type_flag in (' . TRANSACTION_TYPE_BUY . ',' . TRANSACTION_TYPE_SELL . ')
			';

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
			$q .= ' AND ( trx.amount like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.shares like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.buy_price like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.fees like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.commission like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.notes like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.description like "%' . $filter['search'] . '%"';
			$q .= '       OR trx.lot_id like "%' . $filter['search'] . '%"';
			$q .= '     )';
		}		
			
		$q .= '
			ORDER BY trx.transaction_date DESC, trx.id DESC 
		';
	
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date']]);

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
	
    static public function getSymbolArray(&$error)
    {
		// get account list
		$array = [];
		$accountType = isset($accountType) ? $accountType : '%';
		
		try
		{
			$records = Transaction::select('symbol')
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->where('type_flag', TRANSACTION_TYPE_BUY)
				->groupBy('symbol')
				->orderByRaw('symbol')
				->get();
				
			//dd($records);
			
			if (isset($records) && count($records) > 0)
			{
				foreach($records as $record)
					$array[$record->symbol] = $record->symbol;
			}
			else
			{
				$error .= 'No Symbols found';
			}
		}
		catch (\Exception $e) 
		{
			$error .= $e->getMessage();
		}			
					
		return $array;
	}

    static public function getQuote($symbol)
    {
		$quote = null;
		$url = "https://finance.yahoo.com/quote/$symbol?p=$symbol";
		if (true)
		{
			$page = file_get_contents($url);		
			$pos = strpos($page, 'quote-market-notice');
			$text = substr($page, $pos - 175, 200);
			//dump($text);
		}
		else
		{
			//test
			$text = "start>1,900.25<end";
			$text = "start>265.0125<end";
			dump($text);
		}
		
		// match one or more numbers (with optional ',.+-%() ') between '>' and '<', for example: ">1,920.50<" or ">-1.38 (-0.57%)<"
		preg_match_all('/\>[0-9,.\+\-\%\(\) ]+</', $text, $matches); 
		//dump($matches);
		
		// fix up the quote
		$quote = (count($matches) > 0 && count($matches[0]) > 0) ? $matches[0][0] : '';
		$quote = trim($quote, '><');
		$quote = str_replace(',', '', $quote);
		
		// fix up the change
		$change = (count($matches) > 0 && count($matches[0]) > 1) ? $matches[0][1] : '';
		$change = trim($change, '><');
		$change = str_replace(' (', ', ', $change);
		$change = trim($change, ')');
		$up = ($change[0] == '-') ? false : true;
		
		$rc['quote'] = floatval($quote);
		$rc['change'] = $change;
		$rc['up'] = $up;
		
		return $rc;
	}	
}
