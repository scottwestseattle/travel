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


class TestController extends Controller
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

		$tests = array_merge($this->tests, TestController::getTestEntries());

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

		return view('tests.test', $this->getViewData([
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

}
