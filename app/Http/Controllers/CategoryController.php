<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use DB;
use Auth;
use App\Category;

define('PREFIX', 'categories');
define('LOG_MODEL', 'categories');
define('TITLE', 'Category');

class CategoryController extends Controller
{	
	public function __construct ()
	{
		$this->prefix = PREFIX;
		$this->title = TITLE;
		
		parent::__construct();		
	}
	
    public function index(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$records = null;
		
		try
		{
			$records = Category::select()
				->where('parent_id', null)
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->get();
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . ' List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.index', $vdata);
    }	

    public function indexadmin(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
		
		try
		{
			$records = Category::select()
				->where('parent_id', null)
				->where('user_id', Auth::id())
				->where('deleted_flag', 0)
				->orderByRaw('name ASC')
				->get();		
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
					
		$vdata = $this->getViewData([
			'records' => $records,
		]);
			
		return view(PREFIX . '.indexadmin', $vdata);
    }
		
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
		]);
		 
		return view(PREFIX . '.add', $vdata);
	}
		
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
           
		$record = new Category();
		
		$record->user_id = Auth::id();
		$record->name	= $this->trimNull($request->name);
		$record->notes	= $this->trimNull($request->notes);
		
		try
		{
			$record->save();
			Event::logAdd(LOG_MODEL, $record->name, $record->notes, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->name, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
    }

	public function edit(Category $category)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$vdata = $this->getViewData([
			'record' => $category,
		]);		
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Category $category)
    {
		$record = $category;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		
		$record->name = $this->copyDirty($record->name, $request->title, $isDirty, $changes);
		$record->notes = $this->copyDirty($record->notes, $request->notes, $isDirty, $changes);
										
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->name, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'title = ' . $record->name, null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
		}
		else
		{
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'No changes made to ' . $this->title);
		}

		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
	}
	
	public function view(Category $category)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		$vdata = $this->getViewData([
			'record' => $category,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }
	
    public function confirmdelete(Category $category)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $category,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Category $category)
    {	
		$record = $category;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->name, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->name, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index');
    }	

	// this is called by ajax to get subcats when cat is changed
    public function subcategories(Request $request, $category_id)
    {
		$records = Category::getSubcategoryOptions($category_id);
		
		$vdata = $this->getViewDataAjax([
			'records' => $records,
		]);		
		
		return view(PREFIX . '.subcategories', $vdata);
	}
	
}
