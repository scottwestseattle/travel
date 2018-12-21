<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Category extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
	
    static public function getSubcategoryOptions($category_id = null)
    {
		$array = [];

		try
		{
			if (isset($category_id))
			{
				$records = Category::select()
					->where('parent_id', '<>', null)
					->where('user_id', Auth::id())
					->where('deleted_flag', 0)
					->where('parent_id', $category_id)
					->orderByRaw('name')
					->get();
			}

			if (isset($records) && count($records) > 0)
			{
//				if (!isset($array[0]))
//					$array[0] = '(choose subcategory)';
				
				foreach($records as $record)
				{
					$array[$record->id] = $record->name;					
				}
			}
		}
		catch (\Exception $e) 
		{
		}			
			
		return $array;
	}
	
    static public function getArray(&$error, $sub = false)
    {
		// get account list
		$array = [];

		try
		{
			$records = null;
			if ($sub)
			{
				$records = Category::select()
					->where('parent_id', '<>', null)
					->where('user_id', Auth::id())
					->where('deleted_flag', 0)
					->orderByRaw('name')
					->get();
			}
			else
			{
				$records = Category::select()
					->where('parent_id', null)
					->where('user_id', Auth::id())
					->where('deleted_flag', 0)
					->orderByRaw('name')
					->get();
					
				//$records = Category::getSubcategories();
			}

			if (isset($records) && count($records) > 0)
			{
				foreach($records as $record)
				{
					$array[$record->id] = $record->name;
				}
			}
			else
			{
				$error .= 'No ' . ($sub ? 'Subcategories' : 'Categories') . ' found';
			}
		}
		catch (\Exception $e) 
		{
			$error .= $e->getMessage();
		}			
					
		return $array;
	}

    static public function getSubcategories()
    {
		$q = '
			SELECT subs.id, subs.name, subs.amount, subs.parent_id, subs.notes
				, cats.name as category 
			FROM categories as subs
			JOIN categories as cats ON cats.id = subs.parent_id AND cats.user_id = ?
			WHERE 1=1
			AND subs.user_id = ?
			AND subs.deleted_flag = 0
			ORDER BY subs.name ASC 
		';
						
		$records = DB::select($q, [Auth::id(), Auth::id()]);
			
		return $records;
    }	
}
