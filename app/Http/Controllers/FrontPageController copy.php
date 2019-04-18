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
use App\Comment;

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
		$tours = null;
		$tour_count = 0;
		$tourCount = 0;
		$blogCount = 0;
		$locations = null;
		$photosWebPath = null;
		$newWay = false;
		$sliderCount = 0;
		$sliders_h = null;
		$sliders_v = null;
		$sliderPath = null;		
		$posts = null;
		$articles = null;
		$gallery = null;

		$site = Controller::getSite();		
		$showFullGallery = (strtolower($site->site_url) == 'scotthub.com') ? true : false;
				
		//
		// Set up the sections
		//
		$sections = Controller::getSections();
			
		//
		// get tour info
		//
		if (Controller::getArrayByKey(SECTION_TOURS, $sections))
		{
			$tours = $this->getEntriesByType(ENTRY_TYPE_TOUR);

			$tour_count = isset($tours) ? count($tours) : 0;
			$tourCount = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true);
			
			$locations = Location::getPills();

			//
			// get tour page link and main photo
			//
			$photosWebPath = Controller::getPhotoPathRemote('/img/entries/', count($tours) > 0 ? $tours[0]->site_id : 0);
		}
		
		//
		// get the sliders
		//
		if (Controller::getArrayByKey(SECTION_SLIDERS, $sections))
		{
			$newWay = false;
			$sliderCount = 10; // number of sliders to loop through
			$sliders_h = FrontPageController::getSlidersRandom(PHOTO_TYPE_SLIDER_HORIZONTAL_ONLY, $sliderCount, $firstslider);
			$sliders_v = FrontPageController::getSlidersRandom(PHOTO_TYPE_SLIDER_VERTICAL_ONLY, $sliderCount, $firstslider);
			$sliderPath = '/img/sliders/';
		
			if (count($sliders_h) > 0)
				$sliderPath =  Controller::getPhotoPathRemote($sliderPath, $sliders_h[0]->site_id);
			else if (count($sliders_v) > 0)
				$sliderPath =  Controller::getPhotoPathRemote($sliderPath, $sliders_v[0]->site_id);
		}
				
		//
		// get the latest blog posts
		//
		if (Controller::getArrayByKey(SECTION_BLOGS, $sections))
		{
			$posts = $this->getEntriesByType(ENTRY_TYPE_BLOG_ENTRY, true, 6);
			$blogCount = Entry::getEntryCount(ENTRY_TYPE_BLOG, /* $allSites = */ false);
		}
		
		//
		// get the articles
		//
		if (Controller::getArrayByKey(SECTION_ARTICLES, $sections))
		{
			$articles = $this->getEntriesByType(ENTRY_TYPE_ARTICLE, true, 5);
		}
		
		//
		// get the gallery
		//
		if (Controller::getArrayByKey(SECTION_GALLERY, $sections))
		{
			$gallery = $this->getEntriesByType(ENTRY_TYPE_GALLERY, /* approved = */ true, /* limit = */ ($showFullGallery) ? PHP_INT_MAX : 10);
		}
				
		//
		// save visitor stats
		//
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);
		
		$vdata = $this->getViewData([
			'site' => $site,
			'posts' => $posts, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'tourCount' => $tourCount, // todo: why are there two of these counts?
			'sliders_h' => $sliders_h, 
			'sliders_v' => $sliders_v, 
			'newWay' => $newWay,
			'sliders' => $sliders_h, // backwards compatibility
			'slider_path' => $sliderPath,
			'slider_count' => $sliderCount,
			'locations' => $locations, 
			'blogCount' => $blogCount,
			'photoPath' => $photosWebPath, 
			'sections' => $sections,
			'articles' => $articles,
			'gallery' => $gallery,
			'firstslider' => $firstslider,
			'showFullGallery' => $showFullGallery,
		]);
		
    	return view('frontpage.index', $vdata);
    }
    
	private function getSlidersRandom($type_flag, $sliderCount, $firstslider)
	{		
		$sliders = Photo::select()
			->where('parent_id', 0)
			->where('deleted_flag', 0)
			->whereIn('type_flag', [PHOTO_TYPE_SLIDER, intval($type_flag)])
			->orderByRaw('id ASC')
			->get();
			
		$padding = $sliderCount / 2;
		$count = count($sliders);
		if ($count >= ($sliderCount))
		{
			// get a random slider and then only send the 10 sliders on each side of it
			
			$rnd = ($count > 0 && !isset($firstslider)) ? mt_rand(0, $count - 1) : 0;
			
			$first = $rnd - $padding;
			if ($first < 0)
				$first = $count + $first;
				
			$last = $rnd + $padding;
			if ($last > $count)
				$last = -($count - $last);
				
			//dump('count=' . $count . ', rnd=' . $rnd . ', first=' . $first . ', last=' . $last);
				
			// copy the sliders to a new array
			$slice = [];
			for ($i = 0; $i < $sliderCount; $i++)
			{
				$ix = $first + $i;
				
				// need to wrap around?
				if ($ix >= $count)
				{
					$ix -= $count;
				}
				
				$slice[$i] = $sliders[$ix];
			}

			// replace the full list with the short list
			$sliders = $slice;
		}
		
		return $sliders;
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

    public function visitors(Request $request)
    {			
		if (!$this->isAdmin())
             return redirect('/');

		$showBots = false;
		
		if (isset($request->showbots))
			$showBots = true;
			
		$dates = Controller::getDateFilter($request, false, false);
			 
		$filter = Controller::getFilter($request, /* today = */ true);		

		if (false && isset($sort) && $sort == 1)
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
			
			$records = FrontPageController::removeRobots($records, $showBots);
		}
				
		$vdata = $this->getViewData([
			'records' => $records,
			'dates' => Controller::getDateControlDates(),
			'filter' => Controller::getFilter($request, /* today = */ true),
			'bots' => $showBots,
		]);

						
		return view('frontpage.visits', $vdata);
    }

	static protected function removeRobots($records, $showBots = false)
	{    
		$count = 0;
		$out = [];
		
		foreach($records as $record)
		{
			// shorten the user_agent
			$agent = $record->user_agent;
			$new = null;
			
			if (stripos($agent, 'Googlebot') !== FALSE)
				$new = 'GoogleBot';
			else if (stripos($agent, 'Google-Site-Verification') !== FALSE)
				$new = 'GoogleSiteVerification';
			else if (stripos($agent, 'bingbot') !== FALSE)
				$new = 'BingBot';
			else if (stripos($agent, 'mediapartners') !== FALSE)
				$new = 'AdSense';
			else if (stripos($agent, 'a6-indexer') !== FALSE)
				$new = 'Amazon A6';
			else if (stripos($agent, 'pinterest') !== FALSE)
				$new = 'PinBot';					
			else if (stripos($agent, 'yandex.com/bots') !== FALSE)
				$new = 'YandexBot';					
			else if (stripos($agent, 'alphaseobot') !== FALSE)
				$new = 'AlphaSeoBot';
			else if (stripos($agent, 'uptimebot') !== FALSE)
				$new = 'UptimeBot';
			else if (stripos($agent, 'crawl') !== FALSE)
				$new = $agent;
			else if (stripos($agent, 'bot') !== FALSE)
				$new = $agent;
			else if (stripos($record->host_name, 'spider') !== FALSE)
				$new = $record->host_name;
			else if (stripos($record->host_name, 'crawl') !== FALSE)
				$new = $record->host_name;
			else if (stripos($record->host_name, 'bot') !== FALSE)
				$new = $record->host_name;
			else if (stripos($record->host_name, 'googleusercontent.com') !== FALSE)
				$new = 'GoogleUserContent';
			else if (stripos($record->host_name, 'amazonaws.com') !== FALSE)
				$new = 'AmazonAWS';
			else if (stripos($record->referrer, 'localhost') !== FALSE)
				$new = 'localhost';
				
			if (isset($new))
			{
				if (!$showBots)
					continue;

				$record->user_agent = $new;
			}
				
			$out[$count]['date'] = $record->updated_at;
			$out[$count]['id'] = $record->record_id;
			$out[$count]['page'] = $record->page;
			$out[$count]['ref'] = $record->referrer;
			$out[$count]['agent'] = $record->user_agent;
			$out[$count]['host'] = $record->host_name;
			$out[$count]['model'] = $record->model;
			$out[$count]['ip'] = $record->ip_address;
			
			$count++;
		}
		
		return $out;
	}
	
    public function admin()
    {
		if (!$this->isAdmin())
             return redirect('/');

		//
		// get todo list
		//
		$todo = ToolController::getPhotosWithShortNames();
		$linksToFix = ToolController::getLinksToFix();
		$linksToTest = null; //ToolController::getLinksToTest();
		$shortEntries = ToolController::getShortEntries();

		//
		// get unapproved comments
		//
		$comments = Comment::select()
			->where('deleted_flag', 0)
			->where('approved_flag', 0)
			->orderByRaw('id DESC')
			->get();
		if (count($comments) === 0)
			$comments = null;
				
		//
		// get latest events
		//
		$events = Event::getAlerts(10);

		//
		// get blog entries which need action
		//
		$posts = Entry::getBlogEntriesIndexAdmin(/* $pending = */ true);
		
		//
		// get tours which need more info
		//
		$entries = $this->getTourIndexAdmin(/* $pending = */ true);
			
		//
		// get latest users
		//
		$users = User::select()
			->where('user_type', '<=', USER_UNCONFIRMED)
			->orderByRaw('id DESC')
			->get();
					
		//
		// get today's visitors
		//
		$visitors = FrontPageController::removeRobots(Visitor::getVisitors());
			
		$ip = Event::getVisitorIp();
						
		return view('frontpage.admin', $this->getViewData([
			'posts' => $posts,
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors,
			'comments' => $comments, 
			'ip' => $ip, 
			'todo' => $todo,
			'new_visitor' => $this->isNewVisitor(),
			'linksToFix' => $linksToFix,
			'linksToTest' => $linksToTest,
			'shortEntries' => $shortEntries,
			'entryTypes' => Controller::getEntryTypes(),
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
		
		$site_id = $this->getSiteId();

		$entry = Entry::get('page-about');

		if (isset($entry) && isset($entry->description))
		{
			$entry->description = Controller::fixSiteInfo($entry->description, Controller::getSite());
			$entry->description = Controller::formatLinks($entry->description);
		}
		
		$stats = [];
		$stats['photos_content'] = 0;
		$stats['total_pages'] = 0;
		$stats['total_photos'] = 0;
		
		$stats['sliders'] = 0;
		$stats['articles'] = 0;
		$stats['photos_article'] = 0;
		$stats['blogs'] = 0;
		$stats['blog_entries'] = 0;
		$stats['photos_blog'] = 0;
		$stats['photos_post'] = 0;
		$stats['tours'] = 0;
		$stats['photos_tour'] = 0;
		$stats['photos_gallery'] = 0;
		$stats['galleries'] = 0;
		
		$sections = Controller::getSections();
		
		if (Controller::getSection(SECTION_SLIDERS, $sections) != null)
		{
			$stats['sliders'] = Photo::getCountSliders();
		}
		
		if (Controller::getSection(SECTION_ARTICLES, $sections) != null)
		{
			$stats['articles'] = Entry::getEntryCount(ENTRY_TYPE_ARTICLE, /* allSites = */ false);
			$stats['photos_article'] = Photo::getCount(ENTRY_TYPE_ARTICLE);
		}
		
		if (Controller::getSection(SECTION_BLOGS, $sections) != null)
		{
			$stats['blogs'] = Entry::getEntryCount(ENTRY_TYPE_BLOG, /* allSites = */ false);
			$stats['blog_entries'] = Entry::getEntryCount(ENTRY_TYPE_BLOG_ENTRY, /* allSites = */ false);
			$stats['photos_blog'] = Photo::getCount(ENTRY_TYPE_BLOG);
			$stats['photos_post'] = Photo::getCount(ENTRY_TYPE_BLOG_ENTRY);
		}
		
		if (Controller::getSection(SECTION_TOURS, $sections) != null)
		{
			$stats['tours'] = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* allSites = */ false);
			$stats['photos_tour'] = Photo::getCount(ENTRY_TYPE_TOUR);
		}
		
		if (Controller::getSection(SECTION_GALLERY, $sections) != null)
		{
			$stats['galleries'] = Entry::getEntryCount(ENTRY_TYPE_GALLERY, /* allSites = */ false);
			$stats['photos_gallery'] = Photo::getCount(ENTRY_TYPE_GALLERY);		
		}

		$stats['photos_content'] = $stats['photos_article'] + $stats['photos_blog'] + $stats['photos_post'] + $stats['photos_tour'];
		$stats['total_pages'] = $stats['articles'] + $stats['blogs'] + $stats['blog_entries'] + $stats['tours'] + $stats['galleries'];
		$stats['total_photos'] = $stats['sliders'] + $stats['photos_content'] + $stats['photos_gallery']; 
		$stats['total_sitemap_photos'] = $stats['sliders'] + $stats['photos_gallery'];
		
		$stats['static_pages'] = 13;
		$stats['total_sitemap'] = $stats['sliders'] + $stats['total_pages'] + $stats['static_pages'] + $stats['photos_gallery']; 
		
		if ($stats['total_pages'] == 0 && $stats['total_photos'] == 0)
			$stats = null;
		
		// check for an image
		$image = '/img/theme1/about-' . $this->domainName . '.jpg';
		$imagePath = base_path() . '/public' . $image;
		
		$image = (file_exists($imagePath) === TRUE) ? $image : null;

        return view('frontpage.about', $this->getViewData([
			'record' => $entry,
			'stats' => $stats,
			'image' => $image,
		], 'About Page'));
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

		//return view('frontpage.spy', $vdata);
		return redirect('/');
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