<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Transaction;

class Reconcile extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function getRecords()
    {
		$record = Reconcile::select()
			->where('user_id', Auth::id())
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