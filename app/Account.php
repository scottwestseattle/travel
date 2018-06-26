<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Account extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    static public function getArray(&$error)
    {
		// get account list
		$array = [];

		try
		{
			$records = Account::select()
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->orderByRaw('name')
				->get();

			if (isset($records) && count($records) > 0)
			{
				foreach($records as $record)
					$array[$record->id] = $record->name;
			}
			else
			{
				$error .= 'No Accounts found';
			}
		}
		catch (\Exception $e) 
		{
			$error .= $e->getMessage();
		}			
					
		return $array;
	}

    static public function getIndex($showAll = false)
    {
		$q = '
			SELECT a.id, a.name, a.notes, a.hidden_flag, a.starting_balance
				, sum(t.amount) + a.starting_balance as balance 
			FROM accounts as a
			LEFT JOIN transactions as t ON t.parent_id = a.id AND t.deleted_flag = 0 
			WHERE 1=1 
			AND a.user_id = ?
			AND a.deleted_flag = 0
			';
			
		if (!$showAll)
			$q .= ' AND a.hidden_flag = 0 ';
		
		$q .= '
			GROUP BY a.id, a.name, a.notes, a.hidden_flag, a.starting_balance
			ORDER BY a.name ASC
		';
			
		$records = DB::select($q, [Auth::id()]);

		return $records;
    }	
}
