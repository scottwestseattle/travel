<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Transaction;
use App\Account;

class Reconcile extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function account()
    {
    	return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    static public function getIndex($filter)
    {
		$records = Reconcile::select()
			->where('user_id', Auth::id())
			->where('deleted_flag', 0)
			->whereRaw('reconcile_date >= STR_TO_DATE("' . $filter['from_date'] . '", "%Y-%m-%d")')
			->whereRaw('reconcile_date <= STR_TO_DATE("' . $filter['to_date'] . '", "%Y-%m-%d")')
			->orderByRaw('reconcile_date')
			->get();

		return $records;
    }

    static public function getRecords()
    {
		$record = Transaction::select()
			->where('user_id', Auth::id())
			->where('reconciled_flag', 0)
			->where('deleted_flag', 0)
			->orderByRaw('id DESC')
			->get();

		return $record;
    }
    
    static public function get($id)
    {
		$record = null;
		
		return $record;
    }
    

}