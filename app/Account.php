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
}
