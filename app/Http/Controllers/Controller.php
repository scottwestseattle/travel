<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use App\Task;

define('BODY_PLACEHODER', '[[body]]'); // tag that gets replaced with the body of the template

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $viewData = [];
	
	public function __construct ()
	{
	}

	protected function getViewData()
	{
		$taskCount = Task::select()
			->where('user_id', '=', Auth::id())
			->count();

		$this->viewData['taskCount'] = $taskCount;
		
		return $this->viewData;
	}
	
	protected function formatLinks($text)
	{
		$lines = explode("\r\n", $text);
		//dd($text);
		$text = "";
		
		foreach($lines as $line)
		{
				preg_match('/\[(.*?)\]/', $line, $title);		// replace the chars between []
				preg_match('/\((.*?)\)/', $line, $link);	// replace the chars between ()
				
				if ($line === BODY_PLACEHODER)
				{
					dd($line);
					// this is for the template replacement tag 
					$text .= $line;
				}
				else if (sizeof($title) > 0 && sizeof($link)) // if its a link
				{
					$text .= '<a href=' . $link[1] . ' target="_blank">' . $title[1] . '</a><br/>';
				}
				else if (mb_strlen($line) === 0) // blank line
				{
					$text .= $line;
				}
				else // regular line with text
				{
					$text .= $line;
				}
		}
		
		return $text;
	}	
	
}
