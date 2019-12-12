<?php

/*
SELECT count(id) as count, max(id) as id, max(record_id) as record_id, max(model) as model, max(page_url) as page_url
					, max(countryCode) as countryCode, max(updated_at) as updated_at, max(page) as page, ip_address
					, max(referrer) as referrer, max(user_agent) as user_agent, max(host_name) as host_name
				FROM visitors 
				WHERE 1=1 
				GROUP BY ip_address
*/

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use DateTime;

class Visitor extends Base
{
	public function isRobot()
	{  
		$rc = null;
		
		// get the robot list from the settings record
		$robots = Entry::getSetting('settings-user-agent-robots');

		if (isset($robots) && count($robots) > 0)
		{
			// shorten the field
			$agent = $this->user_agent;
			$host = $this->host_name;
			$new = null;
			$found = null;

			// check if $agent is in the robot list
			foreach($robots as $robot => $replacement)
			{
				//$needle = $robot;
				if (($found = Tools::reduceString($robot, $agent, $replacement)) != null)
				{		
					$rc = $found;
					break;
				}
				else if (($found = Tools::reduceString($robot, $host, $replacement)) != null)
				{		
					$rc = $found;
					break;
				}
			}
		}
	
		return isset($rc);
	}
	
    static public function getCountryInfo()
    {
    	$info = [];
		$info['lastCountry'] = 'none';
		$info['lastCountryCode'] = 'none';
		$info['newestCountry'] = 'none';
		$info['newestCountryCode'] = 'none';
		$info['totalCountries'] = 0;
    	
		$record = Visitor::select('country', 'countryCode', 'created_at')
			->where('robot_flag', '!=', 1)
			->whereNotNull('country')
			->orderByRaw('id DESC')
			->first();

		if (isset($record))
		{
			$info['lastCountry'] = $record->country;
			$info['lastCountryCode'] = strtolower($record->countryCode);
		}

		$q = '		
			select * from (
				SELECT min(country) as country, countryCode, min(created_at) as date, count(id) as count
						FROM visitors
						WHERE 1
						AND country is not null
						GROUP BY countryCode
			) AS sub
			ORDER BY date DESC
			';

		$records = DB::select($q);
		//dump($records);

		if (isset($records) && count($records) > 0)
		{					
			$info['newestCountry'] = $records[0]->country;
			$info['newestCountryCode'] = strtolower($records[0]->countryCode);				
		}
		
		$info['totalCountries'] = count($records);
		$info['countries'] = $records;
  	
    	return $info;
	}
	
    static public function getVisitors($date = null)
    {		
		if (isset($date))
		{
			$date = DateTime::createFromFormat('Y-m-d', $date);
			
			$month = intval($date->format("m"));
			$year = intval($date->format("Y"));
			$day = intval($date->format("d"));
		}
		else
		{
			$month = intval(date("m"));
			$year = intval(date("Y"));
			$day = intval(date("d"));
		}

		$fromTime = ' 00:00:00';
		$toTime = ' 23:23:59';
		
		$fromDate = '' . $year . '-' . $month . '-' . $day . ' ' . $fromTime;
		$toDate = '' . $year . '-' . $month . '-' . $day . ' ' . $toTime;

		$q = '
			SELECT *
			FROM visitors 
			WHERE 1=1 
			AND site_id = ?
			AND deleted_flag = 0 
 			AND (created_at >= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s") AND created_at <= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s")) 
			ORDER BY id DESC 
		';
					
		$records = DB::select($q, [SITE_ID, $fromDate, $toDate]);
		
		return $records;
    }
    
    static public function getUniqueVisitors($date = null)
    {		
		if (isset($date))
		{
			$date = DateTime::createFromFormat('Y-m-d', $date);
			
			$month = intval($date->format("m"));
			$year = intval($date->format("Y"));
			$day = intval($date->format("d"));
		}
		else
		{
			$month = intval(date("m"));
			$year = intval(date("Y"));
			$day = intval(date("d"));
		}

		$fromTime = ' 00:00:00';
		$toTime = ' 23:23:59';
		
		$fromDate = '' . $year . '-' . $month . '-' . $day . ' ' . $fromTime;
		$toDate = '' . $year . '-' . $month . '-' . $day . ' ' . $toTime;

		$q = '
			SELECT * from (
				SELECT max(id) as id, max(record_id) as record_id, max(model) as model, max(page_url) as page_url
					, max(countryCode) as countryCode, max(country) as country, max(city) as city
					, max(updated_at) as updated_at, max(page) as page, ip_address
					, max(referrer) as referrer, max(user_agent) as user_agent, max(host_name) as host_name
				FROM visitors 
				WHERE 1=1 
				AND deleted_flag = 0 
				AND (robot_flag = 0 OR robot_flag IS NULL) 
				AND (created_at >= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s") AND created_at <= STR_TO_DATE(?, "%Y-%m-%d %H:%i:%s")) 
				GROUP BY ip_address
 			) as v
			ORDER BY updated_at DESC 
		';
					
		$records = DB::select($q, [$fromDate, $toDate]);
//dd($date);	
		return $records;
    }
}
