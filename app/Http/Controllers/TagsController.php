<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Tag;

class TagsController extends Controller
{
    public function index()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
		$tags = Tag::select()
			//->where('user_id', '=', Auth::id())
			->orderByRaw('tags.id ASC')
			->get();
		
    	return view('tags.index', ['tags' => $tags]);
    }

    public function entries(Tag $tag)
    {		
    	return view('entries.index', ['entries' => $tag->entries]);
    }
	
    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');
		
    	return view('tags.add', ['data' => $this->viewData]);
    }

    public function create(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
    	$tag = new Tag();
    	$tag->name = $request->name;
    	$tag->user_id = Auth::id();
    	$tag->save();
		
    	return redirect('/tags'); 
    }

    public function edit(Tag $tag)
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
    	if (Auth::check())
        {
			return view('tags.edit', ['tag' => $tag, 'data' => $this->viewData]);			
        }
        else 
		{
             return redirect('/tags');
		}            	
    }
	
    public function update(Request $request, Tag $tag)
    {	
		if (!$this->isAdmin())
             return redirect('/');
		 
    	if (Auth::check())
        {
			$tag->name = $request->name;
			$tag->save();
			
			return redirect('/tags/'); 
		}
		else
		{
			return redirect('/');
		}
    }

    public function confirmdelete(Tag $tag)
    {	
		if (!$this->isAdmin())
             return redirect('/');

    	if (Auth::check())
        {			
			return view('tags.confirmdelete', ['tag' => $tag, 'data' => $this->viewData]);				
        }           
        else 
		{
             return redirect('/tags');
		}            	
    }
	
    public function delete(Tag $tag)
    {	
		if (!$this->isAdmin())
             return redirect('/');
	
    	if (Auth::check())
        {			
			$tag->delete();
		}
		
		return redirect('/tags');
    }	
	
}
