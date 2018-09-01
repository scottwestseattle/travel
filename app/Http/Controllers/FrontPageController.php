<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Entry;
use App\Activity;
use App\User;
use App\Photo;
use App\Location;
use App\Visitor;
use App\Site;
use App\Event;

define('PREFIX', 'frontpage');
define('LOG_MODEL', 'frontpage');
define('TITLE', 'Front Page');

class FrontPageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

	public function __construct ()
	{
		parent::__construct();

		$this->prefix = PREFIX;
		//$this->title = $this->domainName . ' - ' . TITLE;
	}
	
    public function first(Request $request)
    {
		return $this->index($request, true);
	}
	
    public function index(Request $request, $firstslider = null)
    {						
		if (Auth::user() && Auth::user()->blocked_flag != 0)
		{
			Auth::logout();
			return redirect('/confirm');
		}

		$posts = null;
				
		//
		// Set up the sections
		//
		$sections = Controller::getSections();
			
		//
		// get tour info
		//
		//$tours = $this->getTourIndex(/* $allSites = */ true);
		$tours = $this->getEntriesByType(ENTRY_TYPE_TOUR, /* approved_flag = */ true, /* limit = */ 0, /* allSites = */ true);

		$tour_count = isset($tours) ? count($tours) : 0;

		$locations = Location::getPills();

		//
		// get tour page link and main photo
		//
		$photosWebPath = Controller::getPhotoPathRemote('/img/entries/', count($tours) > 0 ? $tours[0]->site_id : 0);
		
		//
		// get the sliders
		//
		$sliders = Photo::select()
			//->where('site_id', SITE_ID)
			->where('parent_id', '=', 0)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id ASC')
			->get();
			
		$sliderPath = '/img/sliders/';
		$sliderPath = count($sliders) > 0 ? Controller::getPhotoPathRemote($sliderPath, $sliders[0]->site_id) : $sliderPath;
		
		//
		// get the latest blog posts
		//
		$posts = $this->getEntriesByType(ENTRY_TYPE_BLOG_ENTRY, true, 3);

		//
		// get the articles
		//
		$articles = $this->getEntriesByType(ENTRY_TYPE_ARTICLE, true, 5);

		//
		// get the gallery
		//
		$gallery = $this->getEntriesByType(ENTRY_TYPE_GALLERY, /* approved only = */ true, /* limit = */ 0, /* all_sites = */ true);
		
		//
		// save visitor stats
		//
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);
		
		$vdata = $this->getViewData([
			'site' => Controller::getSite(),
			'posts' => $posts, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'sliders' => $sliders, 
			'slider_path' => $sliderPath,
			'locations' => $locations, 
			'tourCount' => Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true), 
			'blogCount' => Entry::getEntryCount(ENTRY_TYPE_BLOG, /* $allSites = */ false),
			'photoPath' => $photosWebPath, 
			'sections' => $sections,
			'articles' => $articles,
			'gallery' => $gallery,
			'firstslider' => $firstslider,
		]);
		
    	return view('frontpage.index', $vdata);
    }

    public function visits()
    {			
		if (!$this->isAdmin())
             return redirect('/');

		$records = Visitor::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->latest()
			->get();
						
		return view('visits', ['records' => $records]);
    }

    public function visitors(Request $request, $sort = null)
    {			
		if (!$this->isAdmin())
             return redirect('/');

		$dates = Controller::getDateFilter($request, false, false);
			 
		$filter = Controller::getFilter($request, /* today = */ true);		

		if (isset($sort))
		{
			$records = Visitor::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->orderByRaw('updated_at DESC')
				->get();
		}
		else
		{		
			$date = isset($dates['from_date']) ? $dates['from_date'] : null;
		
			$records = Visitor::getVisitors($date);

			/* ORIG
			$records = Visitor::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->latest()
				->get();
			*/
		}
		
		$vdata = $this->getViewData([
			'records' => $records,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
		]);

						
		return view('visits', $vdata);
    }
	
    public function admin()
    {
		if (!$this->isAdmin())
             return redirect('/');

		//
		// get todo list
		//
		$todo = Controller::getPhotosWithShortNames();
		$linksToFix = Controller::getLinksToFix();
		$linksToTest = Controller::getLinksToTest();

		//
		// get latest events
		//
		$events = Event::get(10);

		//
		// get blog entries which need action
		//
		$posts = Entry::getBlogEntriesIndexAdmin(/* $pending = */ true);
		
		//
		// get tours which need more info
		//
		$entries = $this->getTourIndexAdmin(/* $pending = */ true);
			
		//
		// get unconfirmed users
		//
		$users = User::select()
			->where('user_type', '<=', USER_UNCONFIRMED)
			->where('blocked_flag', 0)
			->orderByRaw('id DESC')
			->get();
					
		//
		// get latest visitors
		//
		$visitors = Visitor::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->latest()
			->limit(10)
			->get();
			
		$ip = Event::getVisitorIp();
			
		return view('frontpage.admin', $this->getViewData([
			'posts' => $posts,
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors, 
			'ip' => $ip, 
			'todo' => $todo,
			'new_visitor' => $this->isNewVisitor(),
			'linksToFix' => $linksToFix,
			'linksToTest' => $linksToTest,
		], 'Admin Page'));
    }
	
    public function posts(Entry $entry)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX, $entry->id);

		$entries = Entry::select()
			->where('site_id', SITE_ID)
			//->where('user_id', '=', Auth::id())
			->where('is_template_flag', '<>', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('frontpage.posts', compact('entries'));
    }

    public function tours(Entry $entry)
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX, $entry->id);
		
		$entries = Entry::select()
			->where('site_id', SITE_ID)
			->where('is_template_flag', '=', 1)
			//->orderByRaw('is_template_flag, entries.view_count DESC, entries.title')
			->orderByRaw('entries.id DESC')
			->get();
			
		return view('frontpage.tours', compact('entries'));
    }
	
    /**
     * About page
     */
    public function about()
    {
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_ABOUT);
		
		$entry = Entry::getAboutPage();

		if (isset($entry) && isset($entry[0]) && isset($entry[0]->description))
			$entry[0]->description = Controller::fixSiteInfo($entry[0]->description, Controller::getSite());
		
		$entryStats = Entry::getStats();
		$photoStats = Photo::getStats();
		
		$stats = array_merge($entryStats, $photoStats);
		$stats['total-pages'] = $stats['articles'] + $stats['blogs'] + $stats['blog-entries'] + $stats['tours'];
		$stats['total-photos'] = $stats['sliders'] + $stats['photos']; 
		
		$vdata = $this->getViewData([
			'record' => count($entry) > 0 ? $entry[0] : null,
			'stats' => $stats,
		], 'About Page');
		
        return view('frontpage.about', $vdata);
    }
	
    /**
     * Error page
     */
    public function error()
    {
        return view('frontpage.error');
    }

    public function travelocity()
    {		
		$vdata = $this->getViewData([
			'record' => $this->getTicket(),
		]);
		
        return view('frontpage.travelocity', $vdata);
    }
	
    public function expedia()
    {
		$vdata = $this->getViewData([
			'record' => $this->getTicket(),
		]);
		
        return view('frontpage.expedia', $vdata);
	}

    private function getTicket()
    {
		$record = null;
		
		$r = Entry::select()
			->where('deleted_flag', 0)
			->where('permalink', 'ticket-info')
			->first();
		
		if (isset($r))
		{
			$lines = explode(PHP_EOL, $r->description);
			foreach($lines as $line)
			{
				$parts = explode('=', $line);
				if (strlen($parts[0]) > 0)
				{
					$v = trim($parts[1]);
					$record[$parts[0]] = strlen($v) > 0 ? $v : null;
				}
			}
		}
		
/*
outDate=Mon, Aug 20, 2018
outTimeDepart1=2:30 pm
outTimeArrive1=3:35 pm
outTimeDuration1=2h 5m
outAirportFrom1=Hong Kong Intl. (HKG)
outAirportTo1=Noi Bai Intl. (HAN)
outCityFrom1=Hong Kong (HKG)
outCityTo1=Hanoi (HAN)
outAirline1=Vietnam Airlines
outLogo1=vietair.svg
outFlight1=Flight 593
outOperatedBy1=

outLeg2=
outTimeDepart2=2:30 pm
outTimeArrive2=3:35 pm
outTimeDuration2=2 hrs 18 mins
outAirportFrom2=Atl Hartsfield-Jackson, USA (ATL)
outAirportTo2=Oklahoma City, OK USA (OKC) 
outCityFrom2=Atllanta (ATL)
outCityTo2=Oklahoma City (OKC) 
outAirline2=KLM Royal Dutch Airlines
outLogo2=klm.gif
outFlight2=Flight XXXX
outOperatedBy2=Operated by Delta Airlines	
	
returnDate=Thu, Sep 13, 2018
returnTimeDepart1=10:25 am
returnTimeArrive1=1:30 pm
returnTimeDuration1=2h 5m
returnAirportFrom1=Hong Kong Intl. (HKG)
returnAirportTo1=Noi Bai Intl. (HAN)
returnCityFrom1=Hanoi (HAN)
returnCityTo1=Hong Kong (HKG)
returnAirline1=Vietnam Airlines
returnLogo1=vietair.svg
returnFlight1=Flight 592
returnOperatedBy1=

returnLeg2=
returnTimeDepart2=10:25 am
returnTimeArrive2=1:30 pm
returnTimeDuration2=2 hrs 5 mins, Nonstop
returnAirportFrom2=Hong Kong Intl. (HKG)
returnAirportTo2=Noi Bai Intl. (HAN)
returnCityFrom2=Noi Bai Intl. (HAN)
returnCityTo2=Hong Kong Intl. (HKG)
returnAirline2=Vietnam Airlines
returnLogo2=vietair.svg
returnFlight2=Flight XXXX
returnOperatedBy2=Operated by Delta Connection

priceTotal=$242.50
priceFlight=$183.00
priceTaxes=$59.50

*/
		
		return $record;
	}
	
    public function confirm()
    {	
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_CONFIRM);

		$vdata = $this->getViewData([
		]);
		
		return view('frontpage.confirm', $vdata);
    }

    public function spy(Request $request)
    {	
		session(['spy' => true]);

		$spy = session('spy', null);
		$spy = isset($spy) ? 'ON' : 'OFF';
		
		$vdata = $this->getViewData([
			'spy' => $spy,
		]);
		
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', 'Spy mode is ' . $spy);

		return view('frontpage.spy', $vdata);
    }	

    public function spyoff(Request $request)
    {	
		session(['spy' => null]);

		$spy = session('spy', null);
		$spy = isset($spy) ? 'ON' : 'OFF';
		
		$vdata = $this->getViewData([
			'spy' => $spy,
		]);
		
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', 'Spy mode is ' . $spy);

		return view('frontpage.spy', $vdata);
    }	
}
