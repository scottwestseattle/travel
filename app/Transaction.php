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

    static public function getIndex($subcategory_id = null)
    {
		$q = '
			SELECT trx.id, trx.type_flag, trx.description, trx.amount, trx.transaction_date, trx.parent_id
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
			';
			
		if (isset($subcategory_id))
			$q .= ' AND trx.subcategory_id = ' . $subcategory_id . '';
			
		$q .= '
			ORDER BY trx.transaction_date DESC
		';
		
		$records = DB::select($q, [Auth::id()]);

		return $records;
    }
	
    static public function getTotal($subcategory_id = null)
    {
		$total = 0.0;
		
		$q = '
			SELECT sum(trx.amount) as total
			FROM transactions as trx
			WHERE 1=1
			AND trx.user_id = ?
			AND trx.category_id > 0
			AND trx.subcategory_id > 0
			AND trx.deleted_flag = 0
		';
		
		if (isset($subcategory_id))
			$q .= ' AND trx.subcategory_id = ' . $subcategory_id . '';
				
		$records = DB::select($q, [Auth::id()]);
		if (count($records) > 0)
			$total = $records[0]->total;
		
		return $total;
    }	
}
