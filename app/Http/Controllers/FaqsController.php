<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Faq;

class FaqsController extends Controller
{
    public function index()
    {
		$faqs = Faq::select()
			->where('user_id', '=', Auth::id())
			->orderByRaw('faqs.title ASC')
			->get();
			
		$faqs = $this->formatList($faqs);
		
    	return view('faqs.index', ['faqs' => $faqs, 'data' => $this->getViewData()]);
    }
	
    public function add()
    {
    	return view('faqs.add', ['data' => $this->getViewData()]);
    }

    public function create(Request $request)
    {
    	$faq = new Faq();
    	$faq->title = $request->title;
    	$faq->link = $request->link;
    	$faq->description = $request->description;
    	$faq->user_id = Auth::id();
    	$faq->save();
		
    	return redirect('/faqs'); 
    }

    public function edit(Faq $faq)
    {
    	if (Auth::check() && Auth::user()->id == $faq->user_id)
        {
			return view('faqs.edit', ['faq' => $faq, 'data' => $this->getViewData()]);			
        }
        else 
		{
             return redirect('/faqs');
		}            	
    }
	
    public function update(Request $request, Faq $faq)
    {	
    	if (Auth::check() && Auth::user()->id == $faq->user_id)
        {
			$faq->title = $request->title;
			$faq->link = $request->link;
			$faq->description = $request->description;
			$faq->save();
			
			return redirect('/faqs/'); 
		}
		else
		{
			return redirect('/');
		}
    }

    public function confirmdelete(Faq $faq)
    {	
    	if (Auth::check() && Auth::user()->id == $faq->user_id)
        {			
			return view('faqs.confirmdelete', ['faq' => $faq, 'data' => $this->getViewData()]);				
        }           
        else 
		{
             return redirect('/faqs');
		}            	
    }
	
    public function delete(Faq $faq)
    {	
    	if (Auth::check() && Auth::user()->id == $faq->user_id)
        {			
			$faq->delete();
		}
		
		return redirect('/faqs');
    }	

    public function view(Faq $faq)
    {	
    	if (Auth::check() && Auth::user()->id == $faq->user_id)
        {			
			$faq->description = $this->formatLinks(nl2br($faq->description));
			
			return view('faqs.view', ['faq' => $faq, 'data' => $this->getViewData()]);				
        }           
        else 
		{
             return redirect('/faqs');
		}            	
    }	

    public function search($search)
    {
		$rc = 0;
		$userId = 1;
		$faqs = null;

		if (mb_strlen($search) > 0)
		{
			// strip everything except alpha-numerics, colon, and spaces
			$search = preg_replace("/[^:a-zA-Z0-9 .]+/", "", $search);
		}
		else
		{
			echo 'no search string';
			return $rc;
		}

		if (mb_strlen($search) == 0)
		{
			echo 'no search string';
			return $rc;
		}

		$faqs = Faq::select()->whereRaw('1 = 1')
			->where('user_id', '=', Auth::id())
			->where('title', 'like', '%' . $search . '%')
			->orWhere('description', 'like', '%' . $search . '%')
			->orderBy('title')
			->limit(25)
			->get();

		$faqs = $this->formatList($faqs);
			
		$faqs = compact('faqs');
				
    	return view('faqs.search', $faqs);
	}
	
	private function formatList($faqs)
	{
		foreach($faqs as $faq)
		{
			$faq->description = $this->formatLinks($faq->description);
			
			/*
			$lines = explode("\r\n", $faq);
			$text = "";
			
			foreach($lines as $line)
			{
				preg_match('/\[(.*?)\]/', $line, $title);		// replace the chars between []
				preg_match('/\((.*?)\)/', $line, $link);	// replace the chars between ()
				
				if (sizeof($title) > 0) // if its a link
				{
					$text .= '<a href=' . $link[1] . ' target="_blank">' . $title[1] . '</a><br/>';
				}
				else if (mb_strlen($line) === 0) // blank line
				{
					$text .= $line; // . '\r\n';
				}
				else // regular line with text
				{
					$text .= $line; // . '\r\n';
				}
			}
			*/
		}
		
		return $faqs;
	}
}
