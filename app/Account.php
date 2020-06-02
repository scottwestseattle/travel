<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use DateTime;
use DateInterval;

class Account extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function getStartingBalance($accountId)
    {
    	$accountId = intval($accountId);
    	$startingBalance = 0.0;
    	
		$record = Account::select()
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->where('id', $accountId)
			->orderByRaw('name')
			->first();

		if (isset($record))
		{
			$startingBalance = round(floatval($record->starting_balance), 2);
		}

		return $startingBalance;
	}
		
    static public function getArray(&$error, $accountType)
    {
		// get account list
		$array = [];
		$accountType = isset($accountType) ? $accountType : '%';
		
		try
		{
			$records = Account::select()
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->where('account_type_flag', 'like', $accountType)
				//->where('hidden_flag', 0)
				->orderByRaw('name')
				->get();
//dd($records);
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

    static public function isReconcileOverdue($reconcileDate)
    {
		$rc = false;
		
		if (isset($reconcileDate))
		{
			$nextReconcileDate = $lastReconcileDate = new DateTime($reconcileDate);
			$nextReconcileDate->add(new DateInterval('P1M')); // add 1 month
			$today = new DateTime();
			$today->setTime(0, 0, 0); // zero the time so it will match $nextReconcileDate
			if ($today > $nextReconcileDate)
			{
				// out of date
				$rc = true;
			}
		}
		else
		{
			// never reconciled
			$rc = true;
		}		
		
		return $rc;
	}
	
    static public function getReconcilesOverdue()
    {
		$records = self::getIndex(false, /* $showReconcile = */ true);
		
		$accounts = [];
		
		foreach($records as $record)
		{
			if (self::isReconcileOverdue($record->reconcile_date))
			{
				// out of date
				$accounts[] = $record;
			}
		}
		
		return $accounts;
	}
	
    static public function getIndex($showAll = false, $showReconcile = false)
    {
		$q = '
			SELECT a.id, a.name, a.notes, a.hidden_flag, a.starting_balance, a.reconcile_flag, a.reconcile_statement_day
				, sum(t.amount) + a.starting_balance as balance
				, max(r.reconcile_date) as reconcile_date
			FROM accounts as a
			LEFT JOIN transactions as t ON t.parent_id = a.id AND t.deleted_flag = 0 AND t.reconciled_flag = 1 
			LEFT JOIN reconciles as r ON r.account_id = a.id AND r.deleted_flag = 0
			WHERE 1=1 
			AND a.user_id = ?
			AND a.deleted_flag = 0
			';
			
		if ($showReconcile)
			$q .= ' AND a.reconcile_flag = 1 ';
		else if (!$showAll)
			$q .= ' AND a.hidden_flag = 0 ';

		$q .= '	GROUP BY a.id, a.name, a.notes, a.hidden_flag, a.starting_balance, a.reconcile_flag, a.reconcile_statement_day ';

		$q .= ($showReconcile ? ' ORDER BY reconcile_date ASC, a.name ASC ' : ' ORDER BY a.name ASC ');
		

			
//dd($q);
		$records = DB::select($q, [Auth::id()]);

		return $records;
    }	
}
