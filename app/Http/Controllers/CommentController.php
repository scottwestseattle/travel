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
define('TITLE', 'Comment');

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
				->where('approved_flag', 1)
				->orderByRaw('id DESC')
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
				->orderByRaw('approved_flag ASC, id DESC')
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
		
	public function view(Comment $comment)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_VIEW);

		$vdata = $this->getViewData([
			'record' => $comment,
		]);				
		 
		return view(PREFIX . '.view', $vdata);
    }

    public function add($parent_id = 0)
    {
    	$parent_id = intval($parent_id);
    	
		if (!$this->isAdmin())
             return redirect('/');
		 
		return view(PREFIX . '.add', $this->getViewData([
			'parent_id' => $parent_id,
		]));
	}
		
    public function create(Request $request)
    {		
		$record = new Comment();
		
		$record->site_id = SITE_ID;
		$record->parent_id = $request->parent_id;
				
		$record->name = $this->trimNull($request->name, /* alphanum = */ true);
		$record->comment = $this->trimNull($request->comment, /* alphanum = */ true);

		if (isset($record->name) && isset($record->comment))
		{
			// turn off comments except for admins
			if (!$this->isAdmin() || strpos($record->comment, "http") !== false || strpos($record->comment, ".com") !== false)
			{
				Event::logAdd(LOG_MODEL, 'COMMENT NOT ADDED: ' . $record->name, $record->comment, 0);

				// send spammers away
				return redirect('https://www.booking.com/index.html?aid=1535308');
			}
		}
		else
		{
			$msg = "Comment name or text can't be empty";
			
			Event::logError(LOG_MODEL, LOG_ACTION_ADD, $msg);

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			return back();		
		}
		
		try
		{
			// save the visitor info so we can see where the comments are coming from
			$visitorId = $this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);
			$record->visitor_id = $visitorId;

			$record->save();
			
			Event::logAdd(LOG_MODEL, $record->name, $record->comment, $record->id);
			
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', __('content.Comment Sent For Approval'));
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'Save Error: ' . $record->name, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', 'Error Saving Comment - Please try again');

			// if a spammer entered a long comment, just send them to booking
			return redirect('https://www.booking.com/index.html?aid=1535308');
		}	
			
		return redirect($this->getReferer($request, '/' . PREFIX . '/')); 
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
		
		$record->name = $this->copyDirty($record->name, $request->name, $isDirty, $changes);
		$record->comment = $this->copyDirty($record->comment, $request->comment, $isDirty, $changes);
		
		if (isset($record->name) && isset($record->comment))
		{
		}
		else
		{
			$msg = "Comment name or text can't be empty";
			
			Event::logError(LOG_MODEL, LOG_ACTION_ADD, $msg);

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);		
			
			return back();
		}
					
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
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, 'name = ' . $record->name, null, $e->getMessage());
				
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
			
		return redirect('/' . PREFIX . '/indexadmin');
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
			$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
			
			try
			{
				$record->save();
				Event::logEdit(LOG_MODEL, $record->name, $record->id, 'comment has been approved');			
				
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $this->title . ' has been approved');
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $record->name, null, $e->getMessage());

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
