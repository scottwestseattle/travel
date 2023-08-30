<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Account;
use App\Activity;
use App\Comment;
use App\Event;
use App\Entry;
use App\Location;
use App\Photo;
use App\Site;
use App\Tools;
use App\Transaction;
use App\User;
use App\Visitor;

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
		
		$site = Controller::getSite();		

		//
		// set the template according to the site parameters
		//
		// example: template="gallery"; fpColors="minty, mintyDark, minty"; fpGalleryPhotos="20"; quotes=""; quoteCookieMinutes="10";
		$fpGalleryPhotos = Tools::getOptionInt($site->parameters, 'fpGalleryPhotos', 10);
		$template = Tools::getOption($site->parameters, 'template');
		$showFullGallery = ($template == "gallery");
		$colorMain = 'powerBlue';
		$colorAlt = 'DarkBlue';
		$colorBox = $colorMain;
		$fpColors = Tools::getOption($site->parameters, 'fpColors');

		if (isset($fpColors))
		{
			$v = Tools::getCsv($fpColors, 1);
			$colorMain = (isset($v)) ? $v : $colorMain;
				
			$v = Tools::getCsv($fpColors, 2);
			$colorAlt = (isset($v)) ? $v : $colorAlt;

			$v = Tools::getCsv($fpColors, 3);
			$colorBox = (isset($v)) ? $v : $colorBox;
		}
				
		//
		// Set up the sections
		//
		$sections = Controller::getSections();
		
		//
		// Get locations to show current and latest locations
		//
		$latestLocations = Entry::getLatestLocationsFromBlogEntries();
					
		//
		// get tour info
		//
		$tours = $this->getEntriesByType(ENTRY_TYPE_TOUR);

		$tour_count = isset($tours) ? count($tours) : 0;

		$locations = Location::getPills();

		//
		// get tour page link and main photo
		//
		$photosWebPath = Controller::getPhotoPathRemote('/img/entries/', count($tours) > 0 ? $tours[0]->site_id : 0);
		
		//
		// get the sliders
		//
		$newWay = false;
		$sliderCount = 10; // number of sliders to loop through
		$sliders_h = FrontPageController::getSlidersRandom(PHOTO_TYPE_SLIDER_HORIZONTAL_ONLY, $sliderCount, $firstslider);
		$sliders_v = FrontPageController::getSlidersRandom(PHOTO_TYPE_SLIDER_VERTICAL_ONLY, $sliderCount, $firstslider);
		$sliderPath = '/img/sliders/';
		
		if (count($sliders_h) > 0)
			$sliderPath =  Controller::getPhotoPathRemote($sliderPath, $sliders_h[0]->site_id);
		else if (count($sliders_v) > 0)
			$sliderPath =  Controller::getPhotoPathRemote($sliderPath, $sliders_v[0]->site_id);
		
		//
		// get the latest blog posts
		//
		$posts = $this->getEntriesByType(ENTRY_TYPE_BLOG_ENTRY, true, 6);

		//
		// get the articles
		//
		$articles = $this->getEntriesByType(ENTRY_TYPE_ARTICLE, true, 5, null, true /*, ORDERBY_VIEWS*/);

		//
		// get the gallery
		//
		$gallery = $this->getEntriesByType(ENTRY_TYPE_GALLERY, /* approved = */ true, /* limit = */ $fpGalleryPhotos);
		
		//
		// get latest comments
		//
		$comments = Comment::select()
			->where('deleted_flag', 0)
			->where('approved_flag', 1)
			->where('parent_id', 0)
			->orderByRaw('id DESC')
			->limit(3)
			->get();
		
		$maxTextLength = 100;	
		for($i = 0; $i < count($comments); $i++)
		{
			if (strlen($comments[$i]->comment) > $maxTextLength)
				$comments[$i]->comment = substr($comments[$i]->comment, 0, $maxTextLength) . '...';
		}

		//
		// save visitor stats
		//
		$this->saveVisitor(LOG_MODEL, LOG_PAGE_INDEX);
		
		// get random banner index for fp ad
		$bannerIndex = mt_rand(1, BANNERS_FP_COUNT);
		
		$vdata = $this->getViewData([
			'site' => $site,
			'posts' => $posts, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'sliders_h' => $sliders_h, 
			'sliders_v' => $sliders_v, 
			'newWay' => $newWay,
			'sliders' => $sliders_h, // backwards compatibility
			'slider_path' => $sliderPath,
			'slider_count' => $sliderCount,
			'locations' => $locations, 
			'tourCount' => Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true), 
			'blogCount' => Entry::getEntryCount(ENTRY_TYPE_BLOG, /* $allSites = */ false),
			'photoPath' => $photosWebPath, 
			'sections' => $sections,
			'articles' => $articles,
			'gallery' => $gallery,
			'firstslider' => $firstslider,
			'showFullGallery' => $showFullGallery,
			'colorMain' => $colorMain,
			'colorAlt' => $colorAlt,
			'colorBox' => $colorBox,
			'latestLocations' => $latestLocations['recentLocations'],
			'currentLocation' => $latestLocations['currentLocation'],
			'currentLocationPhoto' => $latestLocations['currentLocationPhoto'],
			'comments' => $comments,
			'geo' => $this->geo(),
			'bannerIndex' => $bannerIndex,
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
						
			if ($count > 0)
			{
				if (isset($firstslider))
				{
					$first = $count - 10;
					$last = $count - 1;
				}
				else
				{
					$rnd = mt_rand(0, $count - 1);
					
					$first = $rnd - $padding;
					if ($first < 0)
						$first = $count + $first;
						
					$last = $rnd + $padding;
					if ($last > $count)
						$last = -($count - $last);
				}
			}
				
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
			
		$filter = Controller::getFilter($request, /* today = */ true, /* month = */ false);		
		$filter['showBots'] = isset($request->showbots);
		$filter['showAll'] = isset($request->showall);
		//dump($filter);
		
		$records = Visitor::getVisitors($filter);
		
		$records = self::removeRobots($records, $filter['showBots']);
		// dd($records);

		$vdata = $this->getViewData([
			'records' => $records,
			'dates' => Controller::getDateControlDates(),
			'filter' => $filter,
		]);

		return view('frontpage.visits', $vdata);
    }

	static protected function removeRobots($records, $showBots = false)
	{    
		$count = 0;
		$out = [];

		// get the robot list from the settings record
		$robots = Entry::getSetting('settings-user-agent-robots');

		foreach($records as $record)
		{
			// shorten the field
			$agent = $record->user_agent;
			$host = $record->host_name;
			$referrer = $record->referrer;
			$new = null;
			$found = null;

			if (isset($robots) && count($robots) > 0)
			{
				// check if $agent is in the robot list
				foreach($robots as $robot => $replacement)
				{
					//$needle = $robot;
					if (($found = Tools::reduceString($robot, $agent, $replacement)) != null)
					{		
						$new = $found;
						break;
					}
					else if (($found = Tools::reduceString($robot, $host, $replacement)) != null)
					{		
						$new = $found;
						break;
					}
					else if (($found = Tools::reduceString($robot, $referrer, $replacement)) != null)
					{		
						$new = $found;
						break;
					}
				}
			}
			
			// check if host name is in the robot list	
			
			if (isset($new))
				; // already set above		
			else if (stripos($record->referrer, 'localhost') !== FALSE)
				$new = 'localhost'; // don't want anything that came from localhost
				
			if (isset($new))
			{
				if (!$showBots)
					continue;

				$record->user_agent = $new;
			}
				
			// save the parts that we want to keep
			$out[$count]['date'] = $record->updated_at;
			$out[$count]['id'] = $record->id;
			$out[$count]['record_id'] = $record->record_id;
			$out[$count]['page'] = $record->page;
			$out[$count]['ref'] = $record->referrer;
			$out[$count]['agent'] = $record->user_agent;
			$out[$count]['host'] = $record->host_name;
			$out[$count]['model'] = $record->model;
			$out[$count]['ip'] = $record->ip_address;
			$out[$count]['url'] = $record->page_url;			
			$out[$count]['count'] = isset($record->ip_count) ? $record->ip_count : null;
			
			$location = '';

			if (isset($record->city))
			    $location = $record->city;

			if (isset($record->country))
			{
			    if (strlen($location) > 0)
			        $location .= ', ';

			    $location .= $record->country;
			}

			$flag = '/img/flags/' . strtolower($record->countryCode) . '.png';
			$flag = '<img height="12" src="' . $flag . '" />';
            $location = (strlen($location) > 0) ? $flag . ' ' . $location : '';
			
			$out[$count]['location'] = $location;

			$count++;
		}

		return $out;
	}

    public function debugTest()
    {
		$comments = Comment::select()
			->where('fake_column', 0)
			->get();
	}
    	
    public function admin()
    {
		if (!$this->isAdmin())
             return redirect('/');

		$this->saveVisitor(LOG_MODEL, LOG_PAGE_ADMIN);
			 
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
		$entries = []; //$this->getTourIndexAdmin(/* $pending = */ true);
			
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
		$visitors = self::removeRobots(Visitor::getVisitors());
		$visitorsTotal = 0;
		foreach($visitors as $record)
		{
			$visitorsTotal += $record['count'];
		}
		//dump($visitors);
		
		//
		// get accounts that need to be reconciled
		//
		$accounts = Account::getReconcilesOverdue();
	        
		$visitorCountryInfo = Visitor::getCountryInfo();

		//
		// get unfinished transactions
		//
		$trx = Transaction::select()
			->where('deleted_flag', 0)
			->where('category_id', CATEGORY_ID_FOOD)
			->where('subcategory_id', SUBCATEGORY_ID_UNKNOWN)
			->get();		

		//
		// get stock quotes
		//
		//	
		$stockQuotes = self::getQuotes();
	
		return view('frontpage.admin', $this->getViewData([
			'posts' => $posts,
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors,
			'visitorsTotal' => $visitorsTotal,
			'comments' => $comments, 
			'geo' => $this->geo(), 
			'todo' => $todo,
			'new_visitor' => $this->isNewVisitor(),
			'linksToFix' => $linksToFix,
			'linksToTest' => $linksToTest,
			'shortEntries' => $shortEntries,
			'entryTypes' => Controller::getEntryTypes(),
			'visitorCountryInfo' => $visitorCountryInfo,
			'bannerIndex' => mt_rand(1, BANNERS_FP_COUNT), // random banner index
			'geoLoadTime' => $this->geo()->loadTime(),
			'ignoreErrors' => $this->ignoreErrors(),
			'accountReconcileOverdue' => count($accounts),
			'trx' => $trx,
			'stockQuotes' => $stockQuotes,
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

    public function booking()
    {
		$visitor = $this->saveVisitor(LOG_MODEL, LOG_PAGE_REGISTER);
		
		if (isset($visitor) && $visitor->robot_flag == 1)
		{
			// don't log robots using the 'register' link because it used to be in the site map
		}
		else
		{
			Event::logWarning(LOG_MODEL, LOG_ACTION_REGISTER, 'user entered register url (' . $this->geo()->ip() . ')');
		}

    	return redirect('https://www.booking.com/index.html?aid=1535308');
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
		$stats['hotels'] = 0;
		
    	$countries = self::getCountries();

		$sections = Controller::getSections();
		
		if (Tools::getSection(SECTION_SLIDERS, $sections) != null)
		{
			$stats['sliders'] = Photo::getCountSliders();
		}
		
		if (Tools::getSection(SECTION_ARTICLES, $sections) != null)
		{
			$stats['articles'] = Entry::getEntryCount(ENTRY_TYPE_ARTICLE, /* allSites = */ false);
			$stats['photos_article'] = Photo::getCount(ENTRY_TYPE_ARTICLE);
		}
	
		if (Tools::getSection(SECTION_BLOGS, $sections) != null)
		{
			$stats['blogs'] = Entry::getEntryCount(ENTRY_TYPE_BLOG, /* allSites = */ false);
			$stats['blog_entries'] = Entry::getEntryCount(ENTRY_TYPE_BLOG_ENTRY, /* allSites = */ false);
			$stats['photos_blog'] = Photo::getCount(ENTRY_TYPE_BLOG);
			$stats['photos_post'] = Photo::getCount(ENTRY_TYPE_BLOG_ENTRY);
		}
		
		if (Tools::getSection(SECTION_TOURS, $sections) != null)
		{
			$stats['tours'] = Entry::getEntryCount(ENTRY_TYPE_TOUR, /* allSites = */ false);
			$stats['photos_tour'] = Photo::getCount(ENTRY_TYPE_TOUR);
		}
		
		if (Tools::getSection(SECTION_GALLERY, $sections) != null)
		{
			$stats['galleries'] = Entry::getEntryCount(ENTRY_TYPE_GALLERY, /* allSites = */ false);
			$stats['photos_gallery'] = Photo::getCount(ENTRY_TYPE_GALLERY);		
		}

		if (Tools::getSection(SECTION_HOTELS, $sections) != null)
		{
			$stats['hotels'] = Entry::getEntryCount(ENTRY_TYPE_HOTEL, /* allSites = */ false);
		}

		$stats['photos_content'] = $stats['photos_article'] + $stats['photos_blog'] + $stats['photos_post'] + $stats['photos_tour'];

		$stats['total_pages'] 
			= $stats['articles'] 
			+ $stats['blogs'] 
			+ $stats['blog_entries'] 
			+ $stats['tours'] 
			+ $stats['galleries']
			+ $stats['hotels'];
			
		$stats['total_photos'] = $stats['sliders'] + $stats['photos_content'] + $stats['photos_gallery']; 
		$stats['total_sitemap_photos'] = $stats['sliders'] + $stats['photos_gallery'];
		
		$stats['static_pages'] = 13;
		$stats['total_sitemap'] = $stats['sliders'] + $stats['total_pages'] + $stats['static_pages'] + $stats['photos_gallery']; 
		
		if ($stats['total_pages'] == 0 && $stats['total_photos'] == 0)
			$stats = null;
		
		// check for jpg image
		$image = '/img/theme1/about-' . $this->domainName . '.jpg';
		$imagePath = base_path() . '/public' . $image;		
		$image = (file_exists($imagePath) === TRUE) ? $image : null;
		
		if (!isset($image))
		{
			// check for png image
			$image = '/img/theme1/about-' . $this->domainName . '.png';
			$imagePath = base_path() . '/public' . $image;		
			$image = (file_exists($imagePath) === TRUE) ? $image : null;
		}

		$visitorCountryInfo = Visitor::getCountryInfo();

        return view('frontpage.about', $this->getViewData([
			'record' => $entry,
			'stats' => $stats,
			'image' => $image,
			'countries' => $countries,
			'visitorCountryInfo' => $visitorCountryInfo,
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
		
		$msg = 'Spy mode is ' . $spy;

		// show some extra geo data
		if ($this->geo()->isValid())
		{
			$gyg = $this->geo()->gygLocation();
			$loc = $this->geo()->location();
			$msg .= ' (' . $gyg . ') (' . $loc . ')';
		}
		
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', $msg);

		return redirect('/');
    }	

    public function debug(Request $request)
    {	
		$debug = isset($_COOKIE['debug']) && $_COOKIE['debug'];
				
		setcookie('debug', !$debug, time() + (86400 * 30), "/");	
		
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', 'Debug mode is ' . ($debug ? 'OFF' : 'ON'));

		return redirect('/admin');
    }	
    
    public function spyoff(Request $request)
    {	
		session(['spy' => null]);

		$spy = session('spy', null);
		$spy = isset($spy) ? 'ON' : 'OFF';
		setcookie('debug', false, time() + (86400 * 30), "/");	

		$vdata = $this->getViewData([
			'spy' => $spy,
		]);
		
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', 'Spy mode is ' . $spy);

		return view('frontpage.spy', $vdata);
    }	
    
	// This function get countries from Blog Entry locations, Photo locations, and the Country List settings entry record.
	static private function getCountries()
	{
		// get the standard country names to display and sort by from settings record
		$standardCountryNames = Entry::getSetting('settings-standard-country-names');

		$locations = Photo::getLocationsFromPhotos($standardCountryNames);
		
		$locations2 = Entry::getLocationsFromEntries($standardCountryNames);
		
		foreach($locations2 as $record)
		{			
			if (!array_key_exists($record->name, $locations))
				$locations[$record->name] = $record->name;
		}
		
		// Get additional country names from settings record
		$locations3 = Entry::getLocationsFromSettings($standardCountryNames);

		foreach($locations3 as $record)
		{			
			if (!array_key_exists($record, $locations))
				$locations[$record] = $record;
		}
		
		$countries = [];
		foreach($locations as $key => $value)
		{
			$countries[] = $value;
		}
		sort($countries);

		return $countries;
	}
	
	protected function getQuotes()
	{
		// VOO, S&P 500 ETF
		// XLY, Consumer Desc
		// XLK, Tech
		// GOOG, Alphabet
		
		$quotes = [];
		$quoteMsg = 'quotes not found';		
		$cookieMinutes = 0;
		$usingCookie = false;
		$isOpen = false;
		
		$site = Controller::getSite();
		if (isset($site))
		{
			// get quote list from site parameters.  format: quotes="VOO|S&P 500 ETF, XLY|Consumer Desc";
			$parm = Tools::getOption($site->parameters, 'quotes');
			
			if (isset($parm) && strlen($parm) > 0)
			{
				// is the exchange open
				$isOpen = false;
				
				// get cookie minutes
				$cookieMinutes = Tools::getOption($site->parameters, 'quoteCookieMinutes');
				$cookieMinutes = isset($cookieMinutes) ? intval($cookieMinutes) : 5; // default to 5 minutes
				$cookieMinutesParm = $cookieMinutes;

				// check if fresh quotes are available
				$status = Tools::getExchangeStatus();
				$isOpen = $status['open'];

				for ($i = 0; $i < 5; $i++)
				{
					$v = Tools::getCsv($parm, $i + 1);
					if (isset($v))
					{
						$symbol = null;
						$nickname = null;
						$v = explode('|', $v);
						if (count($v) > 1)
						{
							$symbol = $v[0];
							$nickname = $v[1];
						}
						else if (count($v) > 0)
						{
							$symbol = $v[0];					
						}
			
						if (isset($symbol))
						{						
							$quote = null;
							
							if ($cookieMinutesParm == 0) // remove cookies and don't use cookies
							{
								// delete cookie by setting it to expire immediately
								setcookie($symbol, '', time() - 60, "/"); 
								
								if (isset($_COOKIE[$symbol]))
								{
									// clear cookie doesn't happen immediately
									// dump('cookie still set');
									// dd($_COOKIE[$symbol]);
								}
							}
							
							if ($cookieMinutesParm > 0 && isset($_COOKIE[$symbol]))
							{
								// get the quote from the cookie
								$cookie = $_COOKIE[$symbol];
								$price = Tools::getWord($cookie, 1, '|');
								$change = Tools::getWord($cookie, 2, '|'); 
								$quote = Transaction::makeQuote($symbol, $nickname, $price, $change);
								$usingCookie = true;
								$cookieMinutes = intval($status['minutes']);
							}
							else
							{
								// update the quote
								$quote = Transaction::getQuote($symbol, $nickname);
								
								if ($isOpen)
								{
									// exchange is open, use default cookie minutes
								}
								else
								{
									// exchange is closed, use minutes until it opens
									$cookieMinutes = intval($status['minutes']);
								}

								// make a cookie for the quote to expire in $cookieMinutes
								if ($cookieMinutes > 0)
								{
									$cookie = $quote['price'] . '|' . $quote['change'];
									setcookie($symbol, $cookie, time() + /* secs = */ ($cookieMinutes * 60), "/");
								}
							}

							$quotes[] = $quote;
						}
					}
				}
			
				if ($usingCookie)
				{
					if (isset($status['msg']))
						$quoteMsg = $status['msg'];
					else
						$quoteMsg = 'stale, next update in ' . $cookieMinutesParm . ' mins';					
				}
				else
				{
					$quoteMsg = ($cookieMinutesParm > 0) ? 'market' : 'forced market';
				}	
			}
		}
		
		return [
			'quotes' => $quotes,
			'quoteMsg' => $quoteMsg,
			'cookieMinutes' => $cookieMinutes,
			'isOpen' => $isOpen,
		];
	}
}