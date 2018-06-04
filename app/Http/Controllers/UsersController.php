<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class UsersController extends Controller
{
	private $redirect = '/users/';
	private $redirect_error = '/';
	
    public function index()
    {
		if (Auth::check())
		{
			if ($this->isAdmin())
			{
				$records = User::select()
					->orderByRaw('id DESC')
					->get();
			}
			else if (Auth::check())
			{
				$records = User::select()
					->where('id', '=', Auth::id())
					->orderByRaw('id DESC')
					->get();
			}

			return view('users.index', ['records' => $records, 'data' => null]);
		}
		else
		{
			return redirect($this->redirect_error);
		}
    }

    public function view(User $user)
    {
    	if ($this->isOwnerOrAdmin($user->id))
        {
			return view('users.view', ['user' => $user, 'data' => null]);			
        }
        else 
		{
			return redirect($this->redirect_error); 
		}            	
    }
	
    public function edit(User $user)
    {
    	if ($this->isOwnerOrAdmin($user->id))
        {
			return view('users.edit', ['user' => $user, 'data' => null]);			
        }
        else 
		{
			return redirect($this->redirect_error); 
		}            	
    }
	
    public function update(Request $request, User $user)
    {	
    	if ($this->isOwnerOrAdmin($user->id))
        {
			$user->name = trim($request->name);
			$user->email = trim($request->email);
			$user->user_type = intval($request->user_type);
			
			$user->save();
			
			return redirect($this->redirect); 
		}
		else
		{
			return redirect($this->redirect_error);
		}
    }

    public function confirmdelete(User $user)
    {	
		if (!$this->isAdmin())
             return redirect('/');
			 		
		return view('users.confirmdelete', ['user' => $user, 'data' => $this->viewData]);				
    }
	
    public function delete(User $user)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$user->deleteSafe();
		
    	return redirect($this->redirect); 
    }	
	
}
