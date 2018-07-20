<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Task;

class TasksController extends Controller
{
    public function index()
    {
		$tasks = Task::select()
			->where('user_id', '=', Auth::id())
			->orderByRaw('tasks.id ASC')
			->get();
		
    	return view('tasks.index', ['tasks' => $tasks, 'data' => $this->getViewData()]);
    }
	
    public function add()
    {
    	return view('tasks.add', ['data' => $this->getViewData()]);
    }

    public function create(Request $request)
    {
    	$task = new Task();
    	$task->description = $request->description;
    	$task->link = $request->link;
    	$task->user_id = Auth::id();
    	$task->save();
		
    	return redirect('/tasks'); 
    }

    public function edit(Task $task)
    {
    	if (Auth::check() && Auth::user()->id == $task->user_id)
        {
			return view('tasks.edit', ['task' => $task, 'data' => $this->getViewData()]);			
        }
        else 
		{
             return redirect('/tasks');
		}            	
    }
	
    public function update(Request $request, Task $task)
    {	
    	if (Auth::check() && Auth::user()->id == $task->user_id)
        {				
			//$task->title = $request->title;
			$task->description = $request->description;
			$task->link = $request->link;
			$task->save();
			
			return redirect('/tasks/'); 
		}
		else
		{
			return redirect('/');
		}
    }

    public function confirmdelete(Task $task)
    {	
    	if (Auth::check() && Auth::user()->id == $task->user_id)
        {			
			return view('tasks.confirmdelete', ['task' => $task, 'data' => $this->getViewData()]);				
        }           
        else 
		{
             return redirect('/tasks');
		}            	
    }
	
    public function delete(Task $task)
    {	
    	if (Auth::check() && Auth::user()->id == $task->user_id)
        {			
			$task->delete();
		}
		
		return redirect('/tasks');
    }	
}
