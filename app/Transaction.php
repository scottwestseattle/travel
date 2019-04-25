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
	
    static public function getTotal($records, $accountId = false)
    {
		$total = 0.0;
		$reconciled = 0.0;
		$noPhotos = 0;
		$rc = [];

		foreach($records as $record)
		{
			$amount = round(floatval($record->amount), 2);

			if ($record->reconciled_flag == 1)
				$reconciled += $amount;
				
			if (!isset($record->photo))
				$noPhotos++;
			
			$total += $amount;
		}
		
		// this has to be done or else it shows -0 because of a tiny fraction
		$total = round($total, 2);
		$reconciled = round($reconciled, 2);
		
		$startingBalance = $accountId ? Account::getStartingBalance($accountId) : 0.0;

		$rc['total'] = $total + $startingBalance;
		$rc['no_photos'] = $noPhotos;
		
		if ($total != $reconciled)
		{
			$rc['reconciled'] = $reconciled + $startingBalance;
		}

		return $rc;
    }

    static public function getFilter($filter)
    {		
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.vendor_memo, trx.notes, trx.reconciled_flag, trx.transfer_id
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory, subcategories.id as subcategory_id 
				, transfer_accounts.name as transfer_account
				, photos.filename as photo
			FROM transactions as trx
			JOIN accounts ON accounts.id = trx.parent_id
			LEFT JOIN accounts AS transfer_accounts ON transfer_accounts.id = trx.transfer_account_id 
			JOIN categories ON categories.id = trx.category_id
			JOIN categories AS subcategories ON subcategories.id = trx.subcategory_id
			LEFT JOIN photos ON photos.parent_id = trx.id
			WHERE 1=1 
			AND trx.user_id = ?
			AND trx.deleted_flag = 0 
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
			AND t.description <> "Transfer" 
			GROUP BY month, sortmonth
			ORDER BY sortmonth DESC
			LIMIT ? 
		';
			
		$records = DB::select($q, [Auth::id(), $limit]);
				
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
		';
			
		$records = DB::select($q, [Auth::id()]);

		if (count($records) > 0)
			$balance = floatval($records[0]->balance);
		
		return $balance;
    }

    static public function getExpenses($filter)
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
			AND t.description <> "Transfer" 
 			AND (t.transaction_date >= STR_TO_DATE(?, "%Y-%m-%d") AND t.transaction_date <= STR_TO_DATE(?, "%Y-%m-%d")) 
			GROUP BY subcategory, category
			ORDER BY c.name ASC 
		;';
			
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date']]);
			
		$totals = [];
		
		// add up the subtotals to get the category total
		foreach($records as $record)
		{
			if (!array_key_exists($record->category, $totals))
			{
				$totals[$record->category] = 0;
				$record->first = 1;
			}
				
			$totals[$record->category] += floatval($record->subtotal);
		}
		
		// put the total on each record for easy access in the view
		foreach($records as $record)
		{
			$record->total = $totals[$record->category];
		}
		
		return $records;
    }	
}
