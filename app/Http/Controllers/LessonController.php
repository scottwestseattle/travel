<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App;
use App\Entry;
use App\Event;
use App\Photo;
use App\Location;
use App\Translation;
use App\Comment;


class LessonController extends Controller
{
    public function index()
    {		
		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

		$records = $this->getEntriesByType(ENTRY_TYPE_LESSON, /* approved = */ false); // get all because they are displayed by super admin
			
		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'Lessons',
		]);
			
    	return view('lessons.index', $vdata);
    }
    
    public function add(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		return view('entries.add', $this->getViewData([
			'type_flag' => ENTRY_TYPE_LESSON,
			'site_id' => $this->getSiteId(),
			'referer' => '/lessons',
		]));
	}

	public function stats(Request $request, $permalink)
    {
		$lessons = Entry::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('title', 'like', 'Capitulo%')
			->get()
		;
				
		$words = [];
		$verbs = [];
		$wc = 0;
		foreach($lessons as $lesson)
		{						
			$t = strip_tags($lesson->description);
			$t = preg_replace('/[^a-z0-9 éóíúñÁá]+/i', '', $t);
		
			$t = explode(' ', strtolower($t));
			$wc += count($t);
			foreach($t as $word)
			{
				$word = trim($word);
				if (array_key_exists($word, $words))
				{
					$words[$word]++;
				}
				else
				{
					$words[$word] = 1;
				}
			
				//dd($words);
			}
			
			dump($lesson->title . ' (words: ' . count($t) . ')');
		}
		
		dump('total words: ' . $wc);
		dump('unique words: ' . count($words));

		arsort($words); // sort by count
		$i = 0;
		foreach($words as $word => $count)
		{
			$i++;
			dump($i . ': ' . $word . ' (' . $count . ')');
		}
		//dd($words);    	

		ksort($words); // alpha sort key
		dd($words);

	}
	
	public function permalink(Request $request, $permalink)
    {		
		$next = null;
		$prev = null;
		
		// get the entry the Laravel way so we can access the gallery photo list
		
		if ($this->isAdmin())
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('permalink', $permalink)
				->first();
		}
		else
		{
			$entry = Entry::select()
				->where('site_id', SITE_ID)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('deleted_flag', 0)
				->where('permalink', $permalink)
				->first();
		}
			
		$gallery = null;
		if (isset($entry))
		{
			$this->countView($entry);
			$gallery = $entry->photos;
		
			// get the entry the mysql way so we can have all the main photo and location info
			//$entry = Entry::getEntry($permalink);
			$entry = Entry::get($permalink); // new way with translation included

			$id = isset($entry) ? $entry->id : null;
			$this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
		}
						
		if (isset($entry))
		{
			$entry->description = nl2br($entry->description);
			$entry->description = $this->formatLinks($entry->description);		
		}
		else
		{
			$msg = 'Permalink Entry Not Found: ' . $permalink;
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
			
            return redirect('/lessons');
		}
		
		$page_title = $entry->title;
		$backLink = null;
		$backLinkText = null;
		if ($entry->type_flag == ENTRY_TYPE_BLOG_ENTRY)
		{
			$page_title = 'Blog Post - ' . $page_title;
			
			if (isset($entry->display_date))
			{
				$next = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id);
				$prev = Entry::getNextPrevBlogEntry($entry->display_date, $entry->parent_id, /* next = */ false);
			}
			else
			{
				$msg = 'Missing Display Date to view record: ' . $entry->id;
				Event::logError(LOG_MODEL_ENTRIES, LOG_ACTION_VIEW, /* title = */ $msg);			
						
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $msg);
			}
		}
		else if ($entry->type_flag == ENTRY_TYPE_LESSON)
		{
			$backLink = '/lessons';
			$backLinkText = __('content.Back to Lessons');
			$page_title = __('ui.Lesson') . ' - ' . $page_title;
		}		
		
		$photos = Photo::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', '<>', 1)
			->where('parent_id', '=', $entry->id)
			->orderByRaw('created_at ASC')
			->get();
			
		$comments = Comment::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->where('approved_flag', 1)
			->where('parent_id', $entry->id)
			->get();

		$vdata = $this->getViewData([
			'record' => $entry, 
			'next' => $next,
			'prev' => $prev,
			'photos' => $photos,
			'gallery' => $gallery,
			'backLink' => $backLink,
			'backLinkText' => $backLinkText,
			'page_title' => $page_title,
			'display_date' => Controller::translateDate($entry->display_date),
			'comments' => $comments,
		]);
		
		return view('lessons.view', $vdata);
	}
}
