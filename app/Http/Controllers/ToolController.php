<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\Entry;
use App\Event;
use App\Photo;
use App\Location;


class ToolController extends Controller
{
	private $tests = [
		['EXPECTED NOT FOUND', '/', ''],
		['Affiliates', '/', ''],
		['Buddha', '/', ''],
		['Exploring', '/', ''],
		['Tours, Hikes, Things To Do', '/', ''],
		['USA', '/', ''],
		['Show All Articles', '/', ''],
		['Show All Blogs', '/', ''],
		['Login', '/login', ''],
		['Register', '/register', ''],
		['Reset Password', '/password/reset', ''],
		['About', '/about', ''],
		['Todos Derechos Reservados', '/gallery', ''],
		['All Rights Reserved', '/galleries', ''],
		['Featured Photos', '/photos/sliders', ''],
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
		];
		
    public function test(Request $request)
    {	
		$executed = null;
		
		//$server = 'http://epictravelguide.com';
		$server = 'http://localhost';
		$server = 'http://grittytravel.com';
		//$server = 'http://hikebikeboat.com';
		//$server = 'http://scotthub.com';

		$tests = array_merge($this->tests, ToolController::getTestEntries());

		if (isset($request->test_server))
		{
			$executed = true;
			$executedTests = [];
			$count = 0;
			for ($i = 0; $i < count($tests); $i++)
			{			
				// if item is checked
				if (isset($request->{'test'.$i}))
				{
					$executedTests[$count][0] = $tests[$i][0];
					$executedTests[$count][1] = $tests[$i][1];
					$executedTests[$count][2] = $this->testPage($request->test_server . $tests[$i][1], $tests[$i][0])['results'];
					$count++;
				}
			}
			
			$tests = $executedTests;
		}

		return view('tools.test', $this->getViewData([
			'records' => $tests,
			'test_server' => $server,
			'executed' => $executed,
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
				if ($record->type_flag == ENTRY_TYPE_BLOG) // blogs don't use permalink
				{
					$tests[] = [substr($record->title, 0, 10), '/' . $type . '/view/' . $record->id, ''];	
				}
				else // everything else uses permalinks
				{
					$tests[] = [substr($record->title, 0, 10), '/' . $type . '/' . $record->permalink, ''];
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

		try
		{
			$text = $this->file_get_contents_curl($url);
			$results['results'] = strpos($text, $expected) === false ? 'EXPECTED NOT FOUND' : 'success';
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
		$data = curl_exec($ch);
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

    protected function getSiteMapPhotos()
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
				$urls[] = '/photos/view/' . $record->id;	
			}
		}
		
		return $urls;
	}
	
    protected function makeSiteMap($domainName)
    {	
		$filename = 'sitemap-' . $domainName . '.txt';

		$urls = [
			'/',
			'/login',
			'/register',
			'/about',
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
		
		if (Controller::getSection(SECTION_SLIDERS, $sections) != null)
		{
			$urls[] = '/photos/sliders';
			$urls = array_merge($urls, ToolController::getSiteMapPhotos());
		}
		
		if (Controller::getSection(SECTION_ARTICLES, $sections) != null)
		{
			$urls[] = '/articles';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_ARTICLE));
		}
		
		if (Controller::getSection(SECTION_BLOGS, $sections) != null)
		{
			$urls[] = '/blogs/index';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_BLOG));
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_BLOG_ENTRY));
		}
		
		if (Controller::getSection(SECTION_TOURS, $sections) != null)
		{
			$urls[] = '/tours/location/2';
			$urls[] = '/tours/location/9';
			$urls[] = '/tours/location/23';
			$urls[] = '/tours/location/25';
			$urls[] = '/tours/index';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_TOUR));
		}
		
		if (Controller::getSection(SECTION_GALLERY, $sections) != null)
		{
			$urls[] = '/galleries';
			$urls = array_merge($urls, ToolController::getSiteMapEntries(ENTRY_TYPE_GALLERY));
		}
		
		if (isset($urls))
		{
			// write the sitemap file
			$siteMap = [];
			
			$server = $domainName;
			
			// file name looks like: sitemap-domain.com.txt
			$myfile = fopen($filename, "w") or die("Unable to open file!");
			
			$server = 'http://' . $server;
			
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
			'scotthub.com',
			'hikebikeboat.com',
			'epictravelguide.com',
			'grittytravel.com',
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
		
		if (isset($request->searchText))
		{
			$search = trim($request->searchText);
			
			if (strlen($search) > 1)
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

		return view('tools.search', $this->getViewData([
			'search' => $search,
			'records' => $records,
			'photos' => $photos,
			'entryTypes' => Controller::getEntryTypes(),
		]));		
	}
	
	static protected function searchEntries($text)
	{			
		$q = '
			SELECT *
			FROM entries
			WHERE 1=1
				AND (title like "%' . $text . '%" OR description like "%' . $text . '%")
				AND site_id = ? 
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
				AND (CHAR_LENGTH(description) < 100 OR description IS NULL)  				
				AND deleted_flag = 0
				AND type_flag in (2,3,4,5)
				AND approved_flag = 1 
				AND published_flag = 1
			ORDER by id DESC
		';

		$records = DB::select($q);
			
		return $records;
	}
	
}