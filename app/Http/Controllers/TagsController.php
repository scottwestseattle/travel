<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Tag;

class TagsController extends Controller
{
    public function index()
    {
		$tags = Tag::select()
			->where('user_id', '=', Auth::id())
			->orderByRaw('tags.id ASC')
			->get();
		
    	return view('tags.index', ['tags' => $tags, 'data' => $this->viewData]);
    }
	
    public function add()
    {
    	return view('tags.add', ['data' => $this->viewData]);
    }

    public function create(Request $request)
    {
    	$tag = new Tag();
    	$tag->name = $request->name;
    	$tag->user_id = Auth::id();
    	$tag->save();
		
    	return redirect('/tags'); 
    }

    public function edit(Tag $tag)
    {
    	if (Auth::check() && Auth::user()->id == $tag->user_id)
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
    	if (Auth::check() && Auth::user()->id == $tag->user_id)
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
    	if (Auth::check() && Auth::user()->id == $tag->user_id)
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
    	if (Auth::check() && Auth::user()->id == $tag->user_id)
        {			
			$tag->delete();
		}
		
		return redirect('/tags');
    }	
	
}
