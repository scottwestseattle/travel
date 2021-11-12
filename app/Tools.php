<?php

namespace App;

use DB;
use App;
use Auth;
use Lang;
use DateTime;

use App\Event;
use App\Ip2locationImport;
use App\User;

class Tools
{
	static private $_ip = null;

	// if string contains $needle, reduce it to $reduce
	static public function reduceString($needle, $haystack, $reduce = null)
	{
		$new = null;
		
		if (stripos($haystack, $needle) !== FALSE)
		{
			if (isset($reduce))
				$new = $reduce; // $needle was found and reduced
			else
				$new = $haystack; // sneedle found with no reducer so return the full name
		}
			
		return $new;
	}
	
	static public function getCountryFromLocation($standardCountryNames, $location)
	{
		$c = explode(',', $location);
		if (count($c) > 2)
		{
			$c = trim($c[2]);
		}
		else if (count($c) > 1)
		{
			$c = trim($c[1]);
		}
		else
		{
			$c = $location;
			//dump($location);
		}
		
		$c = self::getStandardCountryName($standardCountryNames, $c);
			
		//dd($c);
	
		return $c;
	}
	
	//private _countryStandardNames = null;
	static public function getStandardCountryName($standardCountryNames, $country)
	{
		if (array_key_exists($country, $standardCountryNames))
			$country = $standardCountryNames[$country];
		
		return $country;
	}	

    static public function isMobile()
    {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		
		$rc = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
			substr($useragent,0,4)));
			
		return $rc;
	}
	
    static public function getOptionArray($options)
    {
		$arr = [];

		// prompt="Select the correct capital"; reverse-prompt="Select the country for the capital"; question-count=20; text-size="medium";
		$key = '/([a-zA-Z\-^=]*)=\"([^\"]*)/i';
		if (preg_match_all($key, $options, $matches))
		{
			if (count($matches) > 2)
			{
				foreach($matches[1] as $key => $data)
				{
					$arr[$data] = $matches[2][$key];
				}
			}
		}

        return $arr;
    }
	
    static public function getOption($options, $key)
    {
		$r = '';

		// prompt="Select the correct capital"; reverse-prompt="Select the country for the capital"; question-count=20; text-size="medium";
		$key = "/" . $key . '=\"([^\"]*)/';
		if (preg_match($key, $options, $matches))
		{
			//dd($matches);
			if (count($matches) > 1)
			{
				$r = $matches[1];
			}
		}

        return $r;
    }

    static public function itoa($n)
    {
		$rc = '';
		
		$rc .= isset($n) ? intval($n) : 'null';
		
		return $rc;
	}
	
    static public function getSafeArrayInt($array, $key, $default)
    {
		$rc = $default;
		$s = self::getSafeArrayString($array, $key, null);
		if (isset($s))
		{
			$rc = intval($s);
		}
		
		return $rc;
	}
	
    static public function getSafeArrayString($array, $key, $default)
    {
		return self::safeArrayGetString($array, $key, $default);
	}

    static public function getSafeArrayBool($array, $key, $default)
    {
		$rc = $default;
		$s = self::getSafeArrayString($array, $key, null);
		if (isset($s))
		{
			$rc = $s;
		}
		
		return $rc;
	}
	
    static public function safeArrayGetString($array, $key, $default)
    {
        $v = $default;

        if (isset($array) && is_array($array) && array_key_exists($key, $array))
        {
            $v = $array[$key];
        }

        return $v;
    }
	
    static public function cleanHtml($text)
	{
		$v = preg_replace('#style="(.*?)"#is', "", $text); // remove styles
		$v = preg_replace('#<p >#is', "<p>", $v); // fix <p>
		//one time fix: $v = self::convertParens($v);
		//dd($v);
		
		return $v;
	}

    static public function convertParens($text)
	{
		$v = $text;
		
		$v = preg_replace('/\(/is', "[", $v);	// change ( to [
		$v = preg_replace('/\)/is', "]", $v);	// change ) to ]
		//dd($v);
		
		return $v;
	}
	
    static public function getHash($text)
	{
		$s = sha1(trim($text));
		$s = str_ireplace('-', '', $s);
		$s = strtolower($s);
		$s = substr($s, 0, 8);
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

		// add last 2 chars
		$final .= substr($s, 6, 2);

		//echo $final;

		return $final;
	}

	//
	// shortcuts to check admin status
	//
	static public function isAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SITE_ADMIN);
	}	
	
	static public function isSuperAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SUPER_ADMIN);
	}	

    static public function makeNumberArray($start = 1, $end = 10)
    {
		$v = [];

		for ($i = $start; $i < $end; $i++)
			$v[$i] = $i;

		return $v;
	}

    static public function convertToHtml($text)
    {
		$v = $text;

		// check for HTML
		if (strpos($v, '[') !== false)
		{
			//$v = str_replace('[', '<', $v);
			//$v = str_replace(']', '>', $v);
		}
		else if (strpos($v, '<') !== false)
		{
			// has regular html, leave it alone
		}
		else
		{
			// no html so add br's
			$v = nl2br($v);
		}

		return $v;
	}

    static public function convertFromHtml($text)
    {
		$v = $text;

		if (strpos($v, '[') !== false)
		{
			//$v = str_replace('[', '<', $v);
			//$v = str_replace(']', '>', $v);
		}
		else if (strpos($v, '<') !== false)
		{
			// has regular html, so convert it
			//$v = str_replace('<', '[', $v);
			//$v = str_replace('>', ']', $v);
		}
		
		// replace <table border="1">
		$v = preg_replace("/\<table( *)border=\"1\"\>/", "<table class=\"table table-borderless\">", $v);

		return $v;
	}

    static public function copyDirty($to, $from, &$isDirty, &$updates = null, $alphanum = false)
    {
		$from = Tools::trimNull($from, $alphanum);
		$to = Tools::trimNull($to, $alphanum);

		if ($from != $to)
		{
			$isDirty = true;

			if (!isset($updates) || strlen($updates) == 0)
				$updates = '';

			$updates .= '|';

			if (strlen($to) == 0)
				$updates .= '(empty)';
			else
				$updates .= $to;

			$updates .= '|';

			if (strlen($from) == 0)
				$updates .= '(empty)';
			else
				$updates .= $from;

			$updates .= '|  ';
		}

		return $from;
	}

	// if string has non-whitespace chars, then it gets trimmed, otherwise gets set to null
	static protected function trimNull($text, $alphanum = false)
	{
		if (isset($text))
		{
			$text = trim($text);

			if ($alphanum)
			{
				// remove all but the specified chars
				$text = preg_replace("/[^a-zA-Z0-9!@.,()-+=?!' \r\n]+/", "", $text);
			}

			if (strlen($text) === 0)
				$text = null;
		}

		return $text;
	}

    static public function createPermalink($title, $date = null)
    {
		$v = null;

		if (isset($title))
		{
			$v = $title;
		}

		if (isset($date))
		{
			$v .= '-' . $date;
		}

		$v = preg_replace('/[^\da-z ]/i', ' ', $v); // replace all non-alphanums with spaces
		$v = str_replace(" ", "-", $v);				// replace spaces with dashes
		$v = strtolower($v);						// make all lc
		$v = Tools::trimNull($v);					// trim it or null it

		return $v;
	}

	static public function flash($level, $content)
	{
		request()->session()->flash('message.level', $level);
		request()->session()->flash('message.content', $content);
    }

	static public function getIp()
	{
		if (isset(self::$_ip))
			return self::$_ip;
		
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif (!empty($_SERVER["HTTP_CLIENT_IP"]))
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		else
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		
		if (strlen($ip) <= strlen('1.1.1.1'))
		{
            $ip = 'localhost';
        }	
		
		if (false)
		{
			// test data for localhost
			$testIps = [
				//
				// 12 test countries
				//
				"66.146.237.252", 	// 00 Canada with no city
				"59.42.37.137", 	// 01 China (can't use during big test because language will change)
				"187.232.156.219", // 02 Mexico  (can't use during big test because language will change)
				"176.88.23.218", 	// 03 Turkey
				"178.65.167.56", 	// 04 Russia
				"114.119.163.185", 	// 05 Singapore
				"45.139.48.15", 	// 06 Liechtenstein
				"137.226.113.34", 	// 07 Germany
				"47.30.145.5", 		// 08 India
				"186.235.248.161", 	// 09 Brazil
				"46.56.72.44", 		// 10 Belarus
				"37.209.168.26", 	// 11 Bulgaria
				"223.255.255.255",  // 12 Australia (last range in db)
				
				//
				// error tests
				//
				"10.115.8.143",		// Invalid internal IP
				"127.0.0.1",		// localhost
				"localhost",		// localhost
				"two words",		// invalid format
				"12345",			// invalid format
				
			];
			
			$rand = mt_rand(0, count($testIps) - 1);
			//$rand = 2; // china
			//$ip = long2ip(3758096383);
			
			$ip = $testIps[$rand];
		}

		self::$_ip = $ip;
		
		return self::$_ip;
	}
	
	static public function getDomainName()
	{
		$v = null;

		if (array_key_exists("SERVER_NAME", $_SERVER))
		{
			$v = $_SERVER["SERVER_NAME"];

			// trim the duba duba duba
			if (Tools::startsWith($v, 'www.'))
				$v = substr($v, 4);
		}

		return $v;
	}
	
	static public function isLocalhost()
	{
		$ip = self::getIp();

		return ($ip == '::1');
	}	

	static public function trunc($string, $length)
	{
		$ellipsis = '...';
		$newLength = $length - strlen($ellipsis);
		$string = (strlen($string) > $length) ? substr($string, 0, $newLength) . $ellipsis : $string;

		return $string;
	}

	static public function getFlashMessage($msg)
	{		
		$rc = '';		
		$translated = 'translated.'; // prefix to show that it was already translated 
		$flash = 'flash.'; // no prefix means not translated, so lang key will be added

		if (self::startsWith($msg, $translated))
		{
			// already translated
			$rc = substr($msg, strlen($translated)); // remove the prefix
		}
		else
		{	
			// needs translation from the 'flash' section
			$rc = Lang::get($flash . $msg); // add the prefix 'flash' and get the translation
		}

		return $rc;
	}
	
	static public function startsWith($haystack, $needle)
	{
		$rc = false;
		$pos = strpos($haystack, $needle);

		if ($pos === false)
		{
			// not found
		}
		else
		{
			// found, check for pos == 0
			if ($pos === 0)
			{
				$rc = true;
			}
			else
			{
				// found but string doesn't start with it
			}
		}

		return $rc;
	}

	static public function endsWith($haystack, $needle)
	{
		$rc = false;
		$pos = strrpos($haystack, $needle);

		if ($pos === false)
		{
			// not found
		}
		else
		{
			// found, check for pos == 0
			if ($pos === (strlen($haystack) - strlen($needle)))
			{
				$rc = true;
			}
			else
			{
				// found but string doesn't start with it
			}
		}

		return $rc;
	}

	static public function appendFile($filename, $line)
	{
		$rc = false;

		try
		{
			$myfile = fopen($filename, "a") or die("Unable to open file!");

			fwrite($myfile, utf8_encode($line . PHP_EOL));

			fflush($myfile);
			fclose($myfile);

			$rc = true;
		}
		catch (\Exception $e)
		{
			dump('error writing file: ' . $filename);
		}

		return $rc;
	}
	
    static public function translateDate($date)
    {		
		$dateFormat = "%B %e, %Y";
				
		if (App::getLocale() == 'es')
		{
			$dateFormat = "%e " . __('ui.of') . ' ' . __('ui.' . strftime("%B", strtotime($date))) . ", %Y";
			
		}
		else if (App::getLocale() == 'zh')
		{
			// 2019年12月25日
			$dateFormat = "%Y" . __('ui.year') . "%m" . __('ui.month') . "%e" . __('ui.date');
		}
		else
		{
		}	
		
		$date = strftime($dateFormat, strtotime($date));
		
		return $date;
	}

    static public function importGeo($maxLines)
    {		
		//$records = Ip2location::all();
		//$records->truncate();

		// Open the file for reading
		$importFile = "/public/import/IP2LOCATION-LITE-DB3.CSV";
		$file = base_path() . $importFile;
		$rc['endCount'] = false;
		$rc['startCount'] = false;
		$rc['error'] = false;
	
		if (!file_exists($file))
		{
			$rc['error'] = 'Import file not found: ' . $importFile;
			return $rc;
		}
		
		try
		{
			if (($h = fopen($file, "r")) !== FALSE) 
			{
				$max = 0;
				$maxName = '';
				$cnt = 0;

				set_time_limit(0);	// sets it to run as long as needed

				$done = false;
				$start = Ip2locationImport::select()->count();
						
				//dump($start);
				//dump(date('H-i-s') . ' - ' . $cnt);
	
				// Convert each line into the local $data variable
				while (!$done && ($line = fgetcsv($h, 1000, ",")) !== FALSE) 
				{
					if ($cnt >= $start)
					{
						// Read the data from a single line
						$ip = new Ip2locationImport();
			
						$ip->iplongStart = $line[0];			
						$ip->iplongEnd = $line[1];
						$ip->countryCode = $line[2];
						$ip->country = $line[3];
						$ip->region = $line[4];
						$ip->city = $line[5];
					
						if (strlen($ip->city) > $max)
						{
							$max = strlen($ip->city);
							$maxName = $ip->city;
						}
						
						if (false && strlen($ip->region) > $max)
						{
							$max = strlen($ip->region);
							$maxName = $ip->region;
						}
						
						if (strlen($ip->country) > $max)
						{
							$max = strlen($ip->country);
							$maxName = $ip->country;
						}

						try
						{
							$ip->save();
						}
						catch (\Exception $e)
						{
							$rc['error'] = 'Error saving import record - check event log';
							Event::logException(LOG_MODEL_GEO, LOG_ACTION_ADD, 'error saving record', 0, substr($e->getMessage(), 200));
							$done = true;
						}
					}
						
					// limit number of lines so request won't time out
					if (($cnt - $start) >= $maxLines)
						break;

					$cnt++;		
				}
				
				// Close the file
				fclose($h);

				// get the record count
				$rc['endCount'] = Ip2locationImport::select()->count();
				
				//dump('count = ' . $cnt);
				//dump($maxName . ': ' . $max);
			}
		}
        catch (\Exception $e)
		{
			request()->session()->flash('message.level', 'danger');
			request()->session()->flash('message.content', 'Error importing geo: ' . $e->getMessage());			
		}

		return $rc;
	}
	
	static public function getDateTimeRange($date = null)
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
		
		// build the date range
		$dates['from_date'] = '' . $year . '-' . $month . '-' . $day . ' ' . $fromTime;
		$dates['to_date'] = '' . $year . '-' . $month . '-' . $day . ' ' . $toTime;	
			
		return $dates;
	}	
	
	static public function getDateRange($date = null)
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

		// build the date range
		$dates['from_date'] = '' . $year . '-' . $month . '-' . $day;
		$dates['to_date'] = '' . $year . '-' . $month . '-' . $day;	
			
		return $dates;
	}
	
	static public function hasLink($text)
	{
		$rc = false;
		
		if (strstr($text, 'http'))
		{
			$rc = true;
		}
		if (strstr($text, '.com'))
		{
			$rc = true;
		}
		if (strstr($text, '.co'))
		{
			$rc = true;
		}
							
		return $rc;
	}		
}
