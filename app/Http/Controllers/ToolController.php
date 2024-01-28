<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App;
use App\Entry;
use App\Event;
use App\Photo;
use App\Location;
use App\Tools;
use App\Ip2locationImport;

class ToolController extends Controller
{
	//fields: ['Expected Result', 'url to test', 'error message returned']
	private $tests = [
		['EXPECTED NOT FOUND', '/', ''],
		['Comments', '/comments', ''],
		['Countries', '/visitors/countries', ''],
		['Affiliates', '/', ''],
		['Welcome', '/', ''],
		['Show All Galleries', '/', ''],
		['Tours, Hikes, Things To Do', '/', ''],
		['Show All Articles', '/', ''],
		['Show All Blogs', '/', ''],
		['Login', '/login', ''],
		['Reset Password', '/password/reset', ''],
		['Visited', '/about', ''],
		['All Rights Reserved', '/galleries', ''],
		['Prev', '/photos/sliders', ''],
		['Siem Reap', '/photos/view/64', ''],
		['Epic Euro Trip', '/blogs/index', ''],
		['Show All Posts', '/blogs/show/105', ''],
		['Day 71', '/blogs/show/31', ''],
		['Beijing Summer Palace', '/entries/show/157', ''], // prev
		['Thursday, Tienanmen Square', '/entries/show/155', ''], // next
		['Big Asia', '/blogs/show/105', ''], // back to blog
		['Seattle Waterfront to Lake Union', '/tours/index', ''],
		['Seattle', '/tours/location/2', ''],
		['China', '/tours/location/9', ''],
		['Articles', '/articles', ''],
		['Hotels', '/hotels', ''],
		['Hostal', '/entries/hostal-europa-barcelona-spain-2019-11-10', ''],
		['Myanmar', '/entries/recent-locations', ''],
		];
		
		
    public function __construct()
    {
        //$this->middleware('admin')->except([
		//	'sitemap', 
		//]);
		
		parent::__construct();
    }
    
	// The CSS sandbox	
	public function style()
	{
		$records = null;
		
		return view('tools.style', $this->getViewData([
			'records' => $records, // for table styling tests
		]));	
	}
	
    public function test(Request $request)
    {	
		$executed = false;
		
		$url = strtolower(Controller::getSite()->site_url);
		if ($url == 'localhost')
			$server = 'http://' . $url;
		else
			$server = 'https://' . $url;

		$tests = array_merge($this->tests, ToolController::getTestEntries());

		$testCount = isset($_COOKIE['testCount']) ? intval($_COOKIE['testCount']) : 0;
		
		if ($testCount >= count($tests))
			$testCount = 0;

		$start = $testCount;
			
		if (isset($request->test_server))
		{
			$executed = true;
			$executedTests = [];
			$count = 0;
			
			// do only the tests that are checked
			for ($i = 0; $i < count($tests); $i++)
			{			
				// if item is checked
				if (isset($request->{'test'.$i}))
				{
					$results = $this->testPage($request->test_server . $tests[$i][1], $tests[$i][0]);
					if ($results['error'])
						break;
						
					$executedTests[$count][0] = $tests[$i][0];
					$executedTests[$count][1] = $tests[$i][1];
					$executedTests[$count][2] = $results;
					
					$count++;
					
					if ($i == 0)
					{
						// todo: quick fix to reset the cookie
						setcookie('testCount', 0, time() + (60 * 10) /* 10 mins */, "/");
					}
				}
			}
			
			// only run these if none checked; 'testCount' cookie keeps track of progress
			if ($count == 0)
			{
				for ($i = $start; $i < count($tests); $i++)
				{			
					$results = $this->testPage($request->test_server . $tests[$i][1], $tests[$i][0]);
					if ($results['error'])
						break;
					
					$executedTests[$count][0] = $tests[$i][0];
					$executedTests[$count][1] = $tests[$i][1];
					$executedTests[$count][2] = $results;
					
					$count++;
					$testCount++;
					setcookie('testCount', $testCount, time() + (60 * 10) /* 10 mins */, "/");
					
					//temp: first 20 take a lot longer, so abort early
					if ($start == 0 && $count >= 20)
						break;

					if ($start == 20 && $count >= 30)
						break;
						
					if ($count >= 50)
						break;
				}
			}
			else
			{
				// todo: quick fix to reset the cookie
				//setcookie('testCount', 0, time() + (60 * 10) /* 10 mins */, "/");
			}
			
			$tests = $executedTests;
		}

		return view('tools.test', $this->getViewData([
			'records' => $tests,
			'test_server' => $server,
			'executed' => $executed,
			'testCount' => $testCount,
		]));
	}
	
	static protected function getTestEntries()
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND type_flag in (2,3,4,5,8)
				AND deleted_flag = 0
				AND published_flag = 1 
				AND approved_flag = 1
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);

		$tests = [];
		
		foreach($records as $record)
		{
			$entryUrls = Controller::getEntryUrls();
			$type = $entryUrls[$record->type_flag];

			if (isset($type))
			{
				//$expectedText = htmlspecialchars(substr($record->title, 0, 10));
				$expectedText = substr($record->title, 0, 10);

				if ($record->type_flag == ENTRY_TYPE_BLOG) // blogs don't use permalink
				{
					$tests[] = [$expectedText, '/' . $type . '/view/' . $record->id, ''];		
				}
				else // everything else uses permalinks
				{
					$tests[] = [$expectedText, '/' . $type . '/' . $record->permalink, ''];
				}
			}
		}
	
		return $tests;
	}
	
    public function testresults(Request $request)
    {
		$results = [];
				
		for ($i = 0; $i < count($this->tests); $i++)
		{			
			// if item is checked
			if (isset($request->{'test'.$i}))
			{
				$results[] = $this->testPage($request->test_server . $this->tests[$i][1], $this->tests[$i][0]);
			}
		}
		
		return view('tests.test', $this->getViewData([
			'records' => $results,
			'test_server' => $server,
		]));
    }
	
    public function testPage($url, $expected)
    {
		$text = '';
		$results['url'] = $url;
		$results['expected'] = $expected;
		$results['error'] = false;
		
		try
		{
			$text = $this->file_get_contents_curl($url);
			if (!isset($text))
			{
				$results['error'] = true;
				return $results;
			}
			
			$expected = str_replace("&", "&amp;",  $expected);
			$expected = str_replace("'", "&#039;", $expected);

			if (strpos($text, $expected) === false)
			{
				//dd('expected: ' . $expected . '|' . $text);
				$results['results'] = 'EXPECTED NOT FOUND';
				$results['success'] = false;
			}
			else
			{
				$results['results'] = 'Success';
				$results['success'] = true;
			}
		}
		catch (\Exception $e) 
		{
			//$error = $e->getMessage();
			$results['results'] = 'ERROR OPENING PAGE: ' . $url;
		}	
				
		return $results;
	}
	
	private function file_get_contents_curl($url) 
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, 'testing=testing'); // so visitor won't be saved

		$data = null;
		try
		{
			// catch doesn't work for 'Maximum execution time of 120 seconds exceeded'
			$data = curl_exec($ch);
		}
		catch (\Exception $e) 
		{
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', 'Test Timed-out');
		}
		
		curl_close($ch);
		
		return $data;
	}


    protected function getSiteMapEntries($type_flag)
    {
		$urls = [];
	
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND type_flag = ?
				AND site_id = ? 
				AND deleted_flag = 0
				AND published_flag = 1 AND approved_flag = 1
			ORDER by id DESC
		';

		$records = DB::select($q, [$type_flag, SITE_ID]);
		
		if (isset($records))
		{
			$entryUrls = Controller::getEntryUrls();

			foreach($records as $record)
			{
				$type = $entryUrls[$record->type_flag];

				if (isset($type))
				{
					if ($record->type_flag == ENTRY_TYPE_BLOG) // blogs don't use permalink
					{
						$urls[] = '/' . $type . '/view/' . $record->id;	
					}
					else // everything else uses permalinks
					{
						$urls[] = '/' . $type . '/' . $record->permalink;
					}
				}
			}
		}
		
		return $urls;
	}

    protected function getSiteMapSliders()
    {	
		$urls = [];

		$q = '
			SELECT *
			FROM photos
			WHERE 1=1
				AND site_id = ? 
				AND deleted_flag = 0
				AND parent_id = 0
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);
		
		if (isset($records))
		{
			foreach($records as $record)
			{
				//$urls[] = '/photos/view/' . $record->id;
				
				$record = Photo::setPermalink($record);
				$urls[] = '/photos/' . $record->permalink . '/' . $record->id;					
			}
		}
		
		return $urls;
	}
	
    protected function getSiteMapPhotos()
    {	
		$urls = [];

		$q = '
SELECT entries.id, photos.filename, photos.id FROM entries
LEFT JOIN photos
	ON photos.parent_id = entries.id AND photos.deleted_flag = 0 
			WHERE 1=1
				AND entries.site_id = 1
				AND entries.deleted_flag = 0
				AND entries.type_flag = 8
                AND entries.published_flag = 1
                And entries.approved_flag = 1
                AND photos.gallery_flag = 1
			ORDER by entries.id ASC;
		';

		$records = DB::select($q, [SITE_ID]);
		
		if (isset($records))
		{
			foreach($records as $record)
			{
				$record = Photo::setPermalink($record);
				$urls[] = '/photos/' . $record->permalink . '/' . $record->id;					
			}
		}
		
		return $urls;
	}
	
    protected function makeSiteMap($sites)
    {	
    	$http = $sites[0];
    	$domainName = $sites[1];
    	
		$filename = 'sitemap-' . $domainName . '.txt';

		$urls = [
			'/',
			'/login',
			'/about',
			'/visitors/countries',
		];
		
		$site = Controller::getSiteByDomainName($domainName);
		if (!isset($site->id))
		{
			$siteMap['sitemap'] = null; // no records
			$siteMap['server'] = $domainName;
			$siteMap['filename'] = $filename;

			return $siteMap;
		}
			
		$sections = Controller::getSections($site->id);
		
		if (Tools::getSection(SECTION_SLIDERS, $sections) != null)
		{
			$urls[] = '/photos/sliders';
			$urls = array_merge($urls, ToolController::getSiteMapSliders());
		}
		
		if (Tools::getSection(SECTION_ARTICLES, $sections) != null)
		{
			$urls[] = '/articles';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_ARTICLE));
		}
		
		if (Tools::getSection(SECTION_BLOGS, $sections) != null)
		{
			$urls[] = '/blogs/index';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_BLOG));
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_BLOG_ENTRY));
		}
		
		if (Tools::getSection(SECTION_TOURS, $sections) != null)
		{
			$urls[] = '/tours/location/2';
			$urls[] = '/tours/location/9';
			$urls[] = '/tours/location/23';
			$urls[] = '/tours/location/25';
			$urls[] = '/tours/index';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_TOUR));
		}
		
		if (Tools::getSection(SECTION_GALLERY, $sections) != null)
		{
			$urls[] = '/galleries';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_GALLERY));
			
			$urls = array_merge($urls, ToolController::getSiteMapPhotos());
		}
		
		if (Tools::getSection(SECTION_COMMENTS, $sections) != null)
		{
			$urls[] = '/comments';
		}
		
		
		if (isset($urls))
		{
			// write the sitemap file
			$siteMap = [];
			
			$server = $domainName;
			
			// file name looks like: sitemap-domain.com.txt
			$myfile = fopen($filename, "w") or die("Unable to open file!");
			
			$server = $http . $server;
			
			foreach($urls as $url)
			{
				$line = $server . $url;
				$siteMap[] = $line;
				fwrite($myfile, utf8_encode($line . PHP_EOL));
			}

			fclose($myfile);
		}
		
		$rc = [];
		$rc['sitemap'] = $siteMap;
		$rc['filename'] = $filename;
		$rc['server'] = $server;
		
		return $rc;
	}
	
	static protected function getLinksToTest($internal = false)
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND (description like "%http%" OR description like "%](%")
				AND site_id = ? 
				AND deleted_flag = 0
				AND published_flag = 1 AND approved_flag = 1
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);
					
		return $records;
	}

    public function sitemap(Request $request)
    {
		$sites = [
//			['https://', 'grittytravel.com'],
			['https://', 'scottmundo.com'],
//			['http://', 'travel.codespace.us'],
//			['http://', 'codespace.us'],
//			['http://', 'spanish50.com'],
//			['http://', 'english50.com'],
		];
		
		$siteMaps = [];

		foreach($sites as $site)
		{
			$siteMap = $this->makeSiteMap($site);
			
			if (isset($siteMap))
				$siteMaps[] = $siteMap;
		}
		
		return view('tools.sitemap', $this->getViewData([
			'siteMaps' => $siteMaps,
			//'records' => $siteMap['sitemap'],
			//'server' => $siteMap['server'],
			//'filename' => $siteMap['filename'],
			'executed' => null,
			'sitemap' => true,
		]));
	}
	
    public function search(Request $request)
    {
		if (!$this->isAdmin())
             return redirect('/');

		$search = null;
		$records = null;
		$photos = null;
		$hash = null;
		
		if (isset($request->searchText))
		{
			$search = trim($request->searchText);

			if (strlen($search) > 1)
			{
				if (Tools::startsWith($search, '@'))
				{
					$hash = substr($search, 1);
					$hash = self::doHash($hash, date("Y"));
				}
				else
				{
					try
					{
						$records = ToolController::searchEntries($search);
						$photos = ToolController::searchPhotos($search);
					}
					catch (\Exception $e) 
					{
					}
				}
			}
		}

		return view('tools.search', $this->getViewData([
			'search' => $search,
			'records' => $records,
			'photos' => $photos,
			'entryTypes' => Controller::getEntryTypes(),
			'hash' => $hash,
		]));		
	}
	
    public function hash()
    {		
		return view('tools.hash', $this->getViewData([]));	
	}
	
	public function hasher(Request $request)
	{
		$hash = trim($request->get('hash'));
		$year = trim($request->get('year'));
		
		$rc = self::doHash($hash, $year);
		
		return view('tools.hash', $this->getViewData([
			'hash' => $hash,
			'hashed' => $rc['hashed'],
			'hashed2024' => $rc['hashed2024'],
			'year' => $year,
		]));	
	}
	
	static private function doHash($hash, $year)
	{		
		$hashed = ToolController::getHash($hash . $year);		// pre-2024, 8 digits
		$hashed2024 = ToolController::getHash($hash . $year, 12); // 2024, made hashes 12 digits

		if (Tools::startsWith($hash, 'Fir') 
			|| Tools::startsWith($hash, 'Go') 
			|| Tools::startsWith($hash, 'Ya')
			|| Tools::startsWith($hash, 'All')
		)
		{
			$hashed .= '!';
			$hashed2024 .= '!';
		}
		else
		{
			$hashed .= '#';
			$hashed2024 .= '#';
		}

		return [
			'hashed' => $hashed,
			'hashed2024' => $hashed2024,
		];
	}
	
	static protected function searchEntries($text)
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND (title like "%' . $text . '%" OR description like "%' . $text . '%")
				AND site_id = ? 
				AND deleted_flag = 0 
			ORDER by id DESC
		';
		//AND type_flag in (2,3,4,5,8)

		$records = DB::select($q, [SITE_ID]);
					
		return $records;
	}
	
	static protected function searchPhotos($text)
	{			
		$q = '
			SELECT *
			FROM photos
			WHERE 1=1
				AND (title like "%' . $text . '%" OR alt_text like "%' . $text . '%" OR filename like "%' . $text . '%" OR location like "%' . $text . '%")
				AND site_id = ? 
				AND deleted_flag = 0 
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);
					
		return $records;
	}

	static public function getPhotosWithShortNames()
	{			
		$q = '
			SELECT photos.*, DATE_FORMAT(photos.created_at, "%Y-%m-%d") as date, entries.permalink, entries.title as entry_title
			FROM photos
			JOIN entries 
				ON entries.id = photos.parent_id 
				AND entries.deleted_flag = 0 
			WHERE 1=1
				AND (length(filename) < 23 OR filename like "PSX%" OR filename like "20%")
				AND photos.site_id = ? 
				AND photos.deleted_flag = 0
				AND photos.type_flag <> ?
			ORDER by photos.id DESC
		';

		$records = DB::select($q, [SITE_ID, PHOTO_TYPE_RECEIPT]);

		return $records;
	}
	
	static public function getLinksToFix()
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND description like "%epictravelguide.com%"				
				AND site_id = ? 
				AND deleted_flag = 0
			ORDER by id DESC
		';

		$records = DB::select($q, [SITE_ID]);
			
		return $records;
	}

	static public function getShortEntries()
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND deleted_flag = 0
				AND type_flag in (2,3,4,5,8)
				AND (finished_flag = 0 OR finished_flag is null)
			ORDER by id DESC
		';

// original:
//				AND (CHAR_LENGTH(description) < 100 OR description IS NULL)  				
//				AND deleted_flag = 0
//				AND type_flag in (2,3,4,5)
//				AND approved_flag = 1 
//				AND published_flag = 1				

		$records = DB::select($q);
			
		return $records;
	}

	public function language($locale = null)
	{
		if (!isset($locale))
		{
			// if it's not set, clear the session data
			session()->forget('locale');
		}
		else if (ctype_alpha($locale))
		{
			session(['locale' => $locale]);
		}

		return redirect()->back();
	}
	
    static private function getHash($text, $length = 8) // pre-2024, length was 8
	{
		$s = sha1(trim($text));
		$s = str_ireplace('-', '', $s);
		$s = strtolower($s);
		$s = substr($s, 0, $length);
		$final = '';

		for ($i = 0; $i < 6; $i++)
		{
			$c = substr($s, $i, 1);
				
			if ($i % 2 != 0)
			{
				if (ctype_digit($c))
				{
                    if ($i == 1)
                    {
                        $final .= "Q";
                    }
                    else if ($i == 3)
                    {
                        $final .= "Z";
                    }
                    else
                    {
                        $final .= $c;
                    }
				}
				else
				{
					$final .= strtoupper($c);
				}
			}
			else
			{
				$final .= $c;
			}
		}

		// add last 2 or 4 chars
		$final .= substr($s, 6, $length - 6);
		
		//echo $final;
		
		return $final;
	}

    public function phpinfo() 
	{
		phpinfo();
	}
	
    public function eunoticeaccept()
    {
		// set eunotice cookie to show that user has accepted it
		session(['eunotice' => true]);
    }
	
    public function eunoticereset()
    {
		// set eunotice cookie to show that user has accepted it
		session(['eunotice' => false]);
		
		return redirect()->back();		
    }	
    
    public function recentLocations()
    {
		$this->saveVisitor(LOG_MODEL_TOOLS, LOG_PAGE_RECENT_LOCATIONS);

		// get the standard country names to display and sort by from settings record
		$standardCountryNames = Entry::getSetting('settings-standard-country-names');

		$records = Entry::getLocationsFromEntries($standardCountryNames);

		$vdata = $this->getViewData([
			'records' => $records,
			'page_title' => 'Recent Locations',
		]);
			
    	return view('tools.recent-locations', $vdata);
	}
	
	public function wpAdmin()
    {
    	Event::logInfo(LOG_MODEL_TOOLS, LOG_ACTION_REGISTER, "user clicked on wp-admin or wp-admin.php");

    	return redirect('https://www.booking.com?aid=1535306');
	}
	
	public function importGeo()
    {
		$status['endCount'] = false;
		$status['error'] = false;
		$status['startCount'] = false;
		
		try
		{
			$status['startCount'] = Ip2locationImport::select()->count();
		}
		catch (\Exception $e)
		{
			$status['error'] = 'Error counting current records in table ip2locationimport, does it exist?';
			Event::logException(LOG_MODEL_GEO, LOG_ACTION_ADD, 'error getting record count from ip2locationimport', 0, substr($e->getMessage(), 200));
		}	
	
    	return view('tools.importgeo', $this->getViewData([
			'status' => $status,
		]));
	}
	
	public function importGeoAjax()
    {
		$maxLines = (Tools::isLocalHost()) ? 100 : 100000;
		
		$status = Tools::importGeo($maxLines);
		
		$total = intval($status['endCount']);
		$new = intval($status['endCount']) - intval($status['startCount']);
		$error = $status['error'];

		$results = $total . '|' . $new;
		if (isset($error))
			$results .= '|' . $error;
		
		$vdata = $this->getViewDataAjax([
			'importCount' => $results,
		]);		
		
		return view('tools.importgeoajax', $vdata);		
	}	
	
	public function getGeoCount()
    {
    	return view('tools.getgeocount', $this->getViewData([
			'count' => Ip2locationImport::select()->count(),
		]));
	}	
}
