<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Entry;
use App\Activity;
use App\User;
use App\Photo;
use App\Location;
use App\Visitor;
use App\Site;
use App\Event;

define("LONGNAME", "Hike, Bike, Boat");

class FrontPageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * This is the front page
     */
    public function index(Request $request)
    {
		$posts = null;

		//
		// set up the site info
		//
		$page_title = config('app.name', 'Travel Guide');
		
		$site = Controller::getSite();
			
		if (isset($site->site_title))
			$page_title .= ' - ' . $site->site_title;
		
		//
		// Set up the sections
		//
		$sections = Controller::getSections();
			
		//
		// get tour info
		//
		$tours = $this->getTourIndex(/* $allSites = */ true);

		$tour_count = isset($tours) ? count($tours) : 0;

		$locations = Location::getPills();

		//
		// get tour page link and main photo
		//
		$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
		$photosWebPath = '/public/img/entries/';
		
		//
		// get the sliders
		//
		$sliders = Photo::select()
			->where('site_id', SITE_ID)
			->where('parent_id', '=', 0)
			->where('deleted_flag', '=', 0)
			->orderByRaw('id ASC')
			->get();
		
		//
		// get the latest blog posts
		//
		$posts = Entry::getLatestBlogPosts(3);
		//dd($posts);

		//
		// get the articles
		//
		$articles = Entry::getEntriesByType(ENTRY_TYPE_ARTICLE);
		
		//
		// save visitor stats
		//
		$this->saveVisitor();
		
		$vdata = [
			'page_title' => $page_title,
			'site' => $site,
			'posts' => $posts, 
			'tours' => $tours, 
			'tour_count' => $tour_count, 
			'sliders' => $sliders, 
			'locations' => $locations, 
			'tourCount' => Entry::getEntryCount(ENTRY_TYPE_TOUR, /* $allSites = */ true), 
			'blogCount' => Entry::getEntryCount(ENTRY_TYPE_BLOG, /* $allSites = */ false),
			'photoPath' => $photosWebPath, 
			'sections' => $sections,
			'articles' => $articles,
		];
		
    	return view('frontpage.index', $vdata);
    }

    public function visits()
    {			
		$records = Visitor::select()
			->where('site_id', SITE_ID)
			->where('deleted_flag', 0)
			->latest()
			->get();
						
		return view('visits', ['records' => $records]);
    }

    public function visitors($sort = null)
    {			
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
			$records = Visitor::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->latest()
				->get();
		}
		
						
		return view('visits', ['records' => $records]);
    }
	
    public function admin()
    {
		//
		// get latest events
		//
		$events = Event::get(5);

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
			
		$vdata = $this->getViewData([
			'posts' => $posts,
			'events' => $events,
			'records' => $entries, 
			'users' => $users, 
			'visitors' => $visitors, 
			'ip' => $ip, 
			'new_visitor' => $this->isNewVisitor()
		]);
			
		return view('frontpage.admin', $vdata);
    }
	
    public function posts(Entry $entry)
    {
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
		$entry = Entry::getAboutPage();
		
		$vdata = $this->getViewData([
			'record' => count($entry) > 0 ? $entry[0] : null,
		]);
		
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
		$record = [
		
		
			// {{$record['']}}
			// OUTBOUND FLIGHT
			//
			
			'outDate' => 'Mon, Aug 20, 2018',			
			
			// OUTBOUND LEG 1
			
			'outTimeDepart1' => '2:30 pm',
			'outTimeArrive1' => '3:35 pm',	
			'outTimeDuration1' => '2h 5m',	
			'outAirportFrom1' => 'Hong Kong Intl. (HKG)',
			'outAirportTo1' => 'Noi Bai Intl. (HAN)',
			'outCityFrom1' => 'Hong Kong (HKG)',
			'outCityTo1' => 'Hanoi (HAN)',
			'outAirline1' => 'Vietnam Airlines',
			'outLogo1' => 'vietair.svg',
			'outFlight1' => 'Flight 593',
			'outOperatedBy1' => 'Operated by Delta Airlines',
			
			// OUTBOUND LEG 2

			'outLeg2' => false,
			'outTimeDepart2' => '2:30 pm',
			'outTimeArrive2' => '3:35 pm',	
			'outTimeDuration2' => '2 hrs 18 mins',	
			'outAirportFrom2' => 'Atl Hartsfield-Jackson, USA (ATL)',
			'outAirportTo2' => 'Oklahoma City, OK USA (OKC) ',
			'outCityFrom2' => 'Atllanta (ATL)',
			'outCityTo2' => 'Oklahoma City (OKC) ',
			'outAirline2' => 'KLM Royal Dutch Airlines',
			'outLogo2' => 'klm.gif',
			'outFlight2' => 'Flight XXXX',
			'outOperatedBy2' => 'Operated by Delta Airlines',
			
			//
			// RETURN FLIGHT
			//
			
			'returnDate' => 'Thu, Sep 13, 2018',
			
			// RETURN LEG 1
			
			'returnTimeDepart1' => '10:25 am',
			'returnTimeArrive1' => '1:30 pm',	
			'returnTimeDuration1' => '2h 5m',	
			'returnAirportFrom1' => 'Hong Kong Intl. (HKG)',
			'returnAirportTo1' => 'Noi Bai Intl. (HAN)',
			'returnCityFrom1' => 'Hanoi (HAN)',
			'returnCityTo1' => 'Hong Kong (HKG)',
			'returnAirline1' => 'Vietnam Airlines',
			'returnLogo1' => 'vietair.svg',
			'returnFlight1' => 'Flight 592',
			'returnOperatedBy1' => '',
			
			
			// RETURN LEG 2
			
			'returnLeg2' => false,
			'returnTimeDepart2' => '10:25 am',
			'returnTimeArrive2' => '1:30 pm',	
			'returnTimeDuration2' => '2 hrs 5 mins, Nonstop',	
			'returnAirportFrom2' => 'Hong Kong Intl. (HKG)',
			'returnAirportTo2' => 'Noi Bai Intl. (HAN)',
			'returnCityFrom2' => 'Noi Bai Intl. (HAN)',
			'returnCityTo2' => 'Hong Kong Intl. (HKG)',
			'returnAirline2' => 'Vietnam Airlines',
			'returnLogo2' => 'vietair.svg',
			'returnFlight2' => 'Flight XXXX',
			'returnOperatedBy2' => 'Operated by Delta Connection',


			
			//
			// PRICES
			//
			
			'priceTotal' => '$242.50',
			'priceFlight' => '$183.00',
			'priceTaxes' => '$59.50',
		];
		
		return $record;
	}
}
