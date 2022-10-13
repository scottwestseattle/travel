<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use DateTime;
use DateInterval;
use App\Reconcile;

class Account extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

	public function reconciles()
    {
		return $this->hasMany('App\Reconcile', 'account_id')->where('deleted_flag', 0)->orderByRaw('reconcile_date DESC');
    }

	public function getSubtotals()
	{
		$subtotals = [];
		$subtotals['label1'] = '';
		$subtotals['value1'] = '';
		$subtotals['label2'] = '';
		$subtotals['value2'] = '';
		$subtotals['label3'] = '';
		$subtotals['value3'] = '';
		$subtotals['label4'] = '';
		$subtotals['value4'] = '';
		$subtotals['label5'] = '';
		$subtotals['value5'] = '';
		
		if (isset($this->reconciles))
		{
			$r = $this->reconciles->first();
			if (isset($r))
			{
				if (isset($r->subtotal_label1))
					$subtotals['label1'] = $r->subtotal_label1;
				if (isset($r->subtotal1))
					$subtotals['value1'] = $r->subtotal1;
				
				if (isset($r->subtotal_label2))
					$subtotals['label2'] = $r->subtotal_label2;
				if (isset($r->subtotal2))
					$subtotals['value2'] = $r->subtotal2;
				
				if (isset($r->subtotal_label3))
					$subtotals['label3'] = $r->subtotal_label3;
				if (isset($r->subtotal3))
					$subtotals['value3'] = $r->subtotal3;
				
				if (isset($r->subtotal_label4))
					$subtotals['label4'] = $r->subtotal_label4;
				if (isset($r->subtotal4))
					$subtotals['value4'] = $r->subtotal4;
				
				if (isset($r->subtotal_label5))
					$subtotals['label5'] = $r->subtotal_label5;
				if (isset($r->subtotal5))
					$subtotals['value5'] = $r->subtotal5;

				$subtotals['total'] = number_format($r->subtotal1 + $r->subtotal2 + $r->subtotal3 + $r->subtotal4 + $r->subtotal5, 2);
			}
		}
		
		return $subtotals;
	}

    static public function getAccount($accountId)
    {    	
		$record = Account::select()
			->where('deleted_flag', 0)
			->where('id', intval($accountId))
			->first();

		return $record;
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
			$nextReconcileDate = new DateTime($reconcileDate);
			//new: $nextReconcileDate = DateTimeEx::getLocalDateTime($nextReconcileDate);
			$nextReconcileDate->setTime(0, 0, 0);
			$nextReconcileDate->add(new DateInterval('P1M')); // add 1 month
			
			//new: $today = DateTimeEx::getLocalDateTime();
			$today = new DateTime();
			$today->setTime(0, 0, 0); // zero the time so it will match $nextReconcileDate

			if ($today >= $nextReconcileDate)
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
			SELECT b.id, b.name, b.notes, b.hidden_flag, b.starting_balance, b.reconcile_flag, b.reconcile_statement_day, b.balance, max(r.reconcile_date) as reconcile_date FROM (
				SELECT a.id, a.name, a.notes, a.hidden_flag, a.starting_balance, a.reconcile_flag, a.reconcile_statement_day
						, sum(t.amount) + a.starting_balance as balance
					FROM accounts as a
					LEFT JOIN transactions as t ON t.parent_id = a.id AND t.deleted_flag = 0 AND t.reconciled_flag = 1 
					WHERE 1=1 
					AND a.user_id = ?
					AND a.deleted_flag = 0
		';
		
		if ($showReconcile)
			$q .= ' AND a.reconcile_flag = 1 ';
		else if (!$showAll)
			$q .= ' AND a.hidden_flag = 0 ';
		
		$q .= '
					GROUP BY a.id, a.name, a.notes, a.hidden_flag, a.starting_balance, a.reconcile_flag, a.reconcile_statement_day 
					) AS b
				LEFT JOIN reconciles as r ON r.account_id = b.id AND r.deleted_flag = 0
				GROUP BY b.id, b.name, b.notes, b.starting_balance, b.reconcile_flag, b.reconcile_statement_day, b.hidden_flag, b.balance
		';

		$q .= ($showReconcile ? ' ORDER BY reconcile_date ASC, name ASC ' : ' ORDER BY name ASC ');	
			
//dd($q);
		$records = DB::select($q, [Auth::id()]);

		return $records;
    }	
}
