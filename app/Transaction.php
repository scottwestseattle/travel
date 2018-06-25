<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Transaction extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function getByVendor($memo)
    {
		$record = Transaction::select()
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->where('vendor_memo', $memo)
			->orderByRaw('id DESC')
			->first();

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
	
    static public function getTotal($records)
    {
		$total = 0.0;
		$reconciled = 0.0;

		foreach($records as $record)
		{
			$amount = floatval($record->amount);
			
			if ($record->reconciled_flag == 1)
				$reconciled += $amount;
			
			$total += $amount;
		}
		
		$rc = ['total' => $total];
		
		if ($total != $reconciled)
			$rc['reconciled'] = $reconciled;
		
		return $rc;
    }

    static public function getFilter($filter)
    {		
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id, trx.vendor_memo, trx.notes, trx.reconciled_flag
				, accounts.name as account
				, categories.name as category
				, subcategories.name as subcategory, subcategories.id as subcategory_id 
				, transfer_accounts.name as transfer_account 
			FROM transactions as trx
			JOIN accounts ON accounts.id = trx.parent_id
			LEFT JOIN accounts AS transfer_accounts ON transfer_accounts.id = trx.transfer_account_id 
			JOIN categories ON categories.id = trx.category_id
			JOIN categories AS subcategories ON subcategories.id = trx.subcategory_id
			WHERE 1=1 
			AND trx.user_id = ?
			AND trx.deleted_flag = 0 
 			AND (trx.transaction_date >= STR_TO_DATE(?, "%Y-%m-%d") AND trx.transaction_date <= STR_TO_DATE(?, "%Y-%m-%d")) 
		';
		
		if ($filter['account_id'] > 0)
			$q .= ' AND trx.parent_id = ' . intval($filter['account_id']) . '';

		if ($filter['category_id'] > 0)
			$q .= ' AND trx.category_id = ' . intval($filter['category_id']) . '';

		if ($filter['subcategory_id'] > 0)
			$q .= ' AND trx.subcategory_id = ' . intval($filter['subcategory_id']) . '';

		if (isset($filter['search']) && strlen($filter['search']) > 0)
			$q .= ' AND trx.description like "%' . $filter['search'] . '%"';

		if (isset($filter['unreconciled_flag']) && $filter['unreconciled_flag'] == 1)
			$q .= ' AND trx.reconciled_flag = 0 ';
		//else
		//	$q .= ' AND trx.reconciled_flag = 1 ';
		
		$q .= '
			ORDER BY trx.transaction_date DESC, trx.id DESC 
		';
		
		//dd($q);
					
		$records = DB::select($q, [Auth::id(), $filter['from_date'], $filter['to_date']]);
		
		return $records;
    }	
}
