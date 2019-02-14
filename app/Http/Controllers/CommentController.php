<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Event;
use DB;
use Auth;
use App\Comment;

define('PREFIX', 'comments');
define('LOG_MODEL', 'comments');
define('TITLE', 'Comments');

class CommentController extends Controller
{	
	public function __construct()
	{
		parent::__construct();

		$this->prefix = PREFIX;
		$this->title = TITLE;
	}
	
    public function index(Request $request)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);

		$records = null;
		
		try
		{
			$records = Comment::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . ' List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return view(PREFIX . '.index', $this->getViewData([
			'records' => $records,
		]));
    }	

    public function indexadmin(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$records = null;
		
		try
		{
			$records = Comment::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->get();		
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'Error Getting ' . $this->title . '  List', null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return view(PREFIX . '.indexadmin', $this->getViewData([
			'records' => $records,
		]));
    }

    public function permalink(Request $request, $permalink)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_PERMALINK);

		$permalink = trim($permalink);
		
		$record = null;
			
		try
		{
			$record = Comment::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e) 
		{
			$msg = 'Entry Not Found: ' . $permalink;
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			Event::logError(LOG_MODEL, LOG_ACTION_PERMALINK, /* title = */ $msg);
			
			return back();					
		}	

		return view(PREFIX . '.view', $this->getViewData([
			'record' => $record, 
		]));
	}
		
	public function view(Comment $comment)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW);

		$vdata = $this->getViewData([
			'record' => $comment,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }

    public function add()
    {
		if (!$this->isAdmin())
             return redirect('/');
		 
		return view(PREFIX . '.add', $this->getViewData([
			]));
	}
		
    public function create(Request $request)
    {		
		if (!$this->isAdmin())
             return redirect('/');
			
		$record = new Comment();
		
		$record->site_id = SITE_ID;
		$record->user_id = Auth::id();
				
		$record->title					= $this->trimNull($request->title);
		$record->description			= $this->trimNull($request->description);

		try
		{
			$record->save();
			
			Event::logAdd(LOG_MODEL, $record->title, $record->site_url, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been added');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->title, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/indexadmin/')); 
    }

	public function edit(Comment $comment)
    {
		if (!$this->isAdmin())
             return redirect('/');
			
		$vdata = $this->getViewData([
			'record' => $comment,
		]);		
		 
		return view(PREFIX . '.edit', $vdata);
    }
		
    public function update(Request $request, Comment $comment)
    {
		$record = $comment;
		
		if (!$this->isAdmin())
             return redirect('/');
		 
		$isDirty = false;
		$changes = '';
		
		$record->title = $this->copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = $this->copyDirty($record->description, $request->description, $isDirty, $changes);
		
		// example of getting value from radio controls
		//$v = isset($request->radio_sample) ? intval($request->radio_sample) : 0;		
		//$record->radio_sample = $this->copyDirty($record->radio_sample, $v, $isDirty, $changes);		
		
		$record->approved_flag = $this->copyDirty($record->approved_flag, isset($request->approved_flag) ? 1 : 0, $isDirty, $changes);
		$record->published_flag = $this->copyDirty($record->published_flag, isset($request->published_flag) ? 1 : 0, $isDirty, $changes);
						
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->title, $record->id, $changes);			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'title = ' . $record->title, null, $e->getMessage());
				
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
		
    public function confirmdelete(Comment $comment)
    {	
		if (!$this->isAdmin())
             return redirect('/');

		$vdata = $this->getViewData([
			'record' => $comment,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Comment $comment)
    {	
		$record = $comment;
		
		if (!$this->isAdmin())
             return redirect('/');
		
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->title, $record->id);					
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->title, $record->id, $e->getMessage());
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $e->getMessage());		
		}	
			
		return redirect('/' . PREFIX . '/index');
    }	

    public function publish(Request $request, Comment $comment)
    {	
    	if (!$this->isOwnerOrAdmin($comment->user_id))
             return redirect('/');
		
		return view(PREFIX . '.publish', $this->getViewData([
			'record' => $comment,
		]));
    }
	
    public function publishupdate(Request $request, Comment $comment)
    {	
		$record = $comment; 
		
		if (!$this->isAdmin())
             return redirect('/');

    	if ($this->isOwnerOrAdmin($record->user_id))
        {			
			$published = isset($request->published_flag) ? 1 : 0;
			$record->published_flag = $published;
			
			if ($published === 0) // if it goes back to private, then it has to be approved again
				$record->approved_flag = 0;
			else
				$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			try
			{
				$record->save();
				Event::logEdit(LOG_MODEL, $record->title, $record->id, 'published/approved status updated');			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' status has been updated');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->title, null, $e->getMessage());

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}				
			
			return redirect('/' . PREFIX . '/indexadmin');
		}
		else
		{
			return redirect('/');
		}
    }	
}
