<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Event;
use Auth;
use App\Transaction;

define('LOG_MODEL', 'email');
define('TITLE', 'Email');

define("EMAIL_DEFAULT_ACCOUNT_ID", 31);  

class EmailController extends Controller
{		
	private function getAccountId($account)
	{
		$accounts = [
			'2602' => 35, // Chase, old card 2117
			'1397' => 31, // Cap Gray new
			'0370' => 31, // Cap Gray old card '0370', still needed because it's used as an account name
			'5043' => 10, // Cap Blue, old number
			'4427' => 10, // Cap Blue, new number
			'1712' => 14,
		];
			
		$accountId = false;

		if (array_key_exists($account, $accounts))
		{
			$accountId = $accounts[$account];
		}
		else
		{
			dump('account not found: ' . $account);
		}
			
		return $accountId;
	}	

    public function check(Request $request, $debug = false) 
	{	
		$debug = boolval($debug);
		
		// this happens sometimes and the transactions are added with no user id
		if (Auth::id() == null)
		{
			$msg = 'User ID is null';
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'check()', null, $msg);
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			return redirect('/transactions/filter');
		}
			
		if (!function_exists('imap_open'))
		{
			$msg = 'Email is not available on this server';
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'imap', null, $msg);
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			return redirect('/transactions/filter');
		}
			
		$email_account = env('EMAIL_USERNAME');
		$email_password = env('EMAIL_PASSWORD');
		$email_server = env('EMAIL_HOST');
		$email_port = env('EMAIL_PORT');
		$email_driver = env('EMAIL_DRIVER');
		$email_encryption = env('EMAIL_ENCRYPTION');

		if ($email_account == 'spam@scotthub.com')
		{
			//$debug = true;
		}
		
		$flash = '';
		$errors = '';
		$count_trx = 0;
		
		// To connect to imap server on port 993
		$address = '{' . $email_server . ':' . $email_port . '/' . $email_driver . '/' . $email_encryption . '}INBOX';
		
		//$mbox = imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email_account, $email_password);	
		$mbox = null;
		try 
		{
			$mbox = imap_open($address, $email_account, $email_password);
		}
		catch (\Exception $e) 
		{
			$msg = 'Could not open imap stream';
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'imap', null, $msg);
			
			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg);
			
			return redirect('/transactions/filter');
		}
			
		if ($mbox != NULL)
		{
			try 
			{
				$num = imap_num_msg($mbox); 
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL, LOG_ACTION_SELECT, 'imap', null, $e->getMessage());
				
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
				
				return redirect('/transactions/filter');
			}	
			
			//if there is a message in your inbox
			if( $num > 0 )
			{
				//read that mail recently arrived
				//echo imap_qprint(imap_body($imap, $num));
				//die;
			}

			$headers = false;
			if ($num > 0)
				$headers = imap_headers($mbox);

			if ($headers == false)
			{
				// no email found
			}
			else
			{
				$count = 0;
				foreach ($headers as $val) 
				{
					$count++;
					$date = NULL;
					$amount = 0.0;
					$desc = '';
					$add = false;
					$accountId = 0;
					
					if ($this->checkCapital($mbox, $count, $val, $date, $amount, $desc, $accountId, $debug))
					{
						$add = true;
					}
					else if ($this->checkChase($mbox, $count, $val, $date, $amount, $desc, $accountId, $debug))
					{
						$add = true;
					}
					else if ($this->checkPacific($mbox, $count, $val, $date, $amount, $desc, $accountId, $debug))
					{
						$add = true;
					}					
					else if ($this->checkManual($mbox, $count, $val, $date, $amount, $desc, $accountId, $debug))
					{
						$add = true;
					}
					else
					{
					}
									
					if ($add)
					{
						if ($debug)
						{
							echo 'date=' . $date->format('Y-m-d'). '<br/>'; 
							echo 'amount=' . $amount . '<br/>'; 
							echo 'desc=' . $desc . '<br/>'; 
						}
						
						$added = false;
						try
						{
							$added = $this->addTransaction($date, $amount, $desc, $accountId, $debug);
							
							Event::logAdd(LOG_MODEL, $desc, $amount, null);
							
							$request->session()->flash('message.level', 'success');
							$request->session()->flash('message.content', 'Email transaction has been added');

						}
						catch (\Exception $e) 
						{
							Event::logException(LOG_MODEL, LOG_ACTION_ADD, 'title = ' . $desc, null, $e->getMessage());

							$request->session()->flash('message.level', 'danger');
							$request->session()->flash('message.content', $e->getMessage());		
							
							return redirect('/transactions/filter');
						}
																		
						if ($added)
						{
							$count_trx++;

							if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
							{
								// don't delete the email message during dev
							}
							else
							{
								// delete the transaction email
								//die('delete');
								
								if ($debug)
								{
									// don't delete emails during dev
								}
								else
								{
									//die('do not do: imap_delete');
									imap_delete($mbox, $count);
								}
							}

							if ($debug) // only do one at a time
							{
								//break;
							}
						}
					}
					else
					{
						// delete all other emails since they've already been forwarded
						if (!$debug)
							imap_delete($mbox, $count);
					}
				}
			}

			imap_close($mbox, CL_EXPUNGE);
		}
		else
		{
			$errors .= 'Unable to open email';
		}
							
		if (strlen($errors > 0))
		{
			$flash = 'Errors: ' . $errors;
			echo 'flash=' . $flash . '<br/>';
		}
		else
		{
			if ($count_trx > 0)
			{
				$flash = 'Transactions added from Email: ' . $count_trx;
			}
			else
			{
				if ($debug)
					$flash = 'No Email Transactions Found for address: ' . $email_account;
				else
					$flash = 'No Email Transactions Found';
			}
		}
			
		$request->session()->flash('message.level', 'success');
		$request->session()->flash('message.content', $flash);
		
		if ($debug)
		{
			echo 'flash=' . $flash;
			echo "<br/><br/><a href='/transactions/filter/'>Return to Transactions</a>";
			//die;
		}
		else
		{
			return redirect('/transactions/filter');
		}
	}
	
	private function checkManual($mbox, $count, $val, &$date, &$amount, &$desc, $debug) 
	{
		$debug = false;
		
		$rc = false; 
		$subject = 'cash';
					
		$pos = strpos($val, $subject);
										
		$sample = "8/5:Haircut:12.35";
		
		if ($pos !== false && $pos == 44) 
		{
			//die('val=' . $val . ', pos=' . $pos);
			
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
						
			$body_start = 'cash:';
			$pos = strpos($body_raw, $body_start);
			//echo 'pos=' . $pos . '<br/>';
			if ($pos === false)
			{
				die('parse: start key not found in body raw: ' . '<br/>' . $body_raw);
			}
						
			$body_raw = substr($body_raw, $pos + strlen($body_start), 20);
			
			$parts = explode(':', $body_raw);
			
			if ($debug)
				Debugger::dump($parts);
			
												
			// get the amount
			$amount = preg_replace("/[\n\r]/", " ", $parts[2]);
			$amount = explode(" ", $amount); // split on spaces to leave just the amount part
			
			if ($debug)
				Debugger::dump($amount);
				
			$amount = floatval(trim($amount[0], '$'));
			$amount = -$amount;
			
						
			// get the date
			$date = DateTime::createFromFormat('m/d', $parts[0]);
			if ($date == NULL)
			{
				die("Date conversion 1 failed, from text: " . $parts[0]);
			}
									
			// get the description
			$desc = $parts[1];
						
			if ($debug)
			{
				echo 'pos=' . $pos . ', val=' . $val . '<br/>';
				echo 'body=' . $body_raw . '<br/>';
				echo 'desc=' . $desc . '<br/>'; 
				echo 'amount=' . $amount . '<br/>'; 
				echo 'date=' . $date->format('Y-m-d') . '<br/>'; 
				//die('*** end of debug ***');
			}

			//echo 'Record:' . $account_raw . '::' . $amount . '::' . $date->format('Y-m-d') . '::' . $desc;die;
		}
		
		return $rc;
	}
	
	private function checkCapital($mbox, $count, $val, &$date, &$amount, &$desc, &$accountId, $debug)
	{
		if ($debug)
		{
			echo '<br/>' . '*** DEBUG, checkCapital() ***' . '<br/>';
		}
		
		$rc = false; 

		//
		// check to see who it's from
		//
		$from = 'Capital';
		$pos = strpos($val, $from);
		if ($pos === false) 
		{
			return $rc; // not a cap account
		}
		
		//
		// now check the message body
		//					
		$subject = 'A new transaction was';
		$pos = strpos($val, $subject);
		
		if ($debug)
			dump('pos=' . $pos);
									
		$sample = "A purchase was charged to your account. RE: Account ending in 6789 As requested, we're notifying you that on 9/29/2020, at USA*CANTEEN VENDING, a pending authorization or purchase in the amount of $1.00 was placed or charged on your Capital One VISA SIGNATURE account.";
				
		if ($pos !== false && $pos >= 44) 
		{
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
			$body_raw = preg_replace("/[^A-Za-z0-9\/\.\'\, ]/", '', $body_raw); // remove all of the trash
			
			$pos = strpos($body_raw, substr($sample, 0, 30));
			//echo 'pos=' . $pos . '<br/>';
			if ($pos === false)
			{
				echo 'body raw = ' . $body_raw . '<br/>';
				echo 'sample = ' . substr($sample, 0, 30) . '<br/>';
				die('*** DEBUG, parse: info text not found in body raw ***');
			}
				
			$body_full = $body_raw; // the account number is getting filtered for some reason but it is still deep in the body, so use the full body to search for it later
			$body_raw = substr($body_raw, $pos, strlen($sample));
												
			// get the amount
			$amount = $this->parseTag($body_raw, 'purchase in the amount of ', 10, 0); 
			$amount = floatval(trim($amount, '$'));
			$amount = -$amount;

			// try this date format: 11/08/2018
			$date_raw = $this->parseTag($body_raw, 'notifying you that on ', 10, -1); 
			$date2 = str_replace(',', '', trim($date_raw));
			$date = DateTime::createFromFormat('m/d/Y', $date2);
			// dump('|' . $date2 . '|');	
					
			if ($date == NULL)
			{
				// date may look like: SEP 30, 2016
				$date_raw = $this->parseTag($body_raw, 'notifying you that on ', 12, -1); 
				$date2 = str_replace(',', '', $date_raw);
				$date = DateTime::createFromFormat('M d Y', $date2);

				if ($date == NULL)
				{
					die("Date conversion 2 failed, from text: " . $date2);
				}
			}

			$date_raw = $date2;
									
			// get the account number, last four digits, it will be within the text in $account

			$account = $this->parseTag($body_full, 'Account ending in', 20, -1); 
			$matches = [];			
			preg_match('/\d{4}/', $account, $matches, PREG_OFFSET_CAPTURE);
			$account = (count($matches) > 0) ? $matches[0][0] : '';
			$accountId = $this->getAccountId($account);
			if (!$accountId)
			{
				dump($account);
				dump($body_full);
				dd($accountId);
			}

			// get the description
			$desc = $this->parseTag($body_raw, $date_raw . ', at ', 30, -1); 
			$pieces = explode(',', $desc);
			
			$desc = $pieces[0];
			if ($debug)
			{
				echo 'date=' . $date_raw . '<br/>';
				echo 'amount=' . $amount . '<br/>';
				echo 'desc=' . $desc . '<br/>'; 
				echo 'accountId=' . $account . '<br/>'; 
				die('*** end of debug ***');
			}
			
			//echo 'Record:' . $account_raw . '::' . $amount . '::' . $date->format('Y-m-d') . '::' . $desc;
		}
		
		return $rc;
	}
	
	private function checkChase($mbox, $count, $val, &$date, &$amount, &$desc, &$accountId, $debug) 
	{
		$rc = false; 
		$sample = "Amazon Rewards Visa";	// Email body - to find the details we need
		//$debug = true;
		
		//
		// check to see who it's from
		//
		$from = 'Chase';
		$pos = strpos($val, $from);
		if ($pos === false) 
		{
			return $rc; // not a cap account
		}

		//
		// check the body for key text
		//						
		$subject = 'Your $';				// Email subject / to find the beginning
		$pos = strpos($val, $subject);					

		//echo '<br/>' . $val . '<br/>pos=' . $pos; die;
		if ($pos !== false && $pos == 44) 
		{
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
						
			$pos = strpos($body_raw, $sample);
			if ($pos === false)
			{
				echo $body_raw . '<br/>';
				die('parse: info text not found in body raw');
			}
			//echo 'pos=' . $pos . '<br/>';die;
						
			$body_raw = substr($body_raw, $pos, 200 /* all the content we need is within 200 chars */);

			$parts = explode(" | ", str_replace("\r\n", "", $body_raw));
			
			/* parts looks like:
			
				  0 => "Amazon Rewards Visa (...2602)"
				  1 => "||=20| Date"
				  2 => "Jul 29, 2021 at 4:17 PM ET"
				  3 => "||=20| Merchant"
				  4 => "AMZN Mktp US"
				  5 => "||=20| Amount"
				  6 => "$35.27"
				  7 => "| || You are receiving this alert"

			*/			
			
			if (count($parts) != 8)
			{
				die('parse: unexpected format found in body');			
			}
				
			//								
			// get the amount
			//
			$amount = floatval(trim($parts[6], '$'));
			$amount = -$amount;
		
			// get the account number, last four digits
			$account = $this->parseTag($parts[0], 'Amazon Rewards Visa (...', 4, -1); 
			$accountId = $this->getAccountId($account);
			if (!$accountId)
			{
				dump($account);
				dump($body_full);
				dd($accountId);
			}
			
			//			
			// get the date
			//
			$date_raw = substr($parts[2], 0, 12); 
			$date2 = str_replace(',', '', trim($date_raw));
			
			// try date format like: "Mar 21 2020"
			$date = DateTime::createFromFormat('M d Y', $date2);
			if ($date == NULL)
			{
				// try normal date format like: "03/21/2020"
				$date = DateTime::createFromFormat('m/d/Y', $date2);
				if ($date == NULL)
				{
					// try date format like: "Mar 21, 2020"
					$date = DateTime::createFromFormat('M d, Y', $date2);
					if ($date == NULL)
					{
						dump($body_raw);
						dump('date: ' . $date2);
						die("Date conversion 3 failed, from text: " . $date2);
					}					
				}
			}
			//dump($date_raw);
			//dd($date2);
				
			//						
			// get the description
			//
			$desc = $parts[4];
						
			if ($debug)
			{
				echo 'Date: |' . $date->format('Y-m-d') . '|<br/>';
				echo 'Account: |' . $account . '|<br/>'; 
				echo 'AccountId: |' . $accountId . '|<br/>'; 
				echo 'Desc: |' . $desc . '|<br/>'; 
				echo 'Amount: |' . $amount . '|<br/>'; 
				die('*** end of debug ***');
			}

			//echo 'Record::' . $account . '::' . $amount . '::' . $date->format('Y-m-d') . '::' . $desc . '<BR/>';
		}
		
		return $rc;
	}
	
	private function checkPacific($mbox, $count, $val, &$date, &$amount, &$desc, &$accountId, $debug) 
	{
		$rc = false; 
		$subject = 'You_paid';
		
		if ($debug) dump($val);
			
		$pos = strpos($val, $subject);
		if ($debug) dump($pos);
			
		$sample = "Hello, Scott Wilkinson PayPal You paid $8.34 USD to WASABI KING'S MALL HAM.  Thanks for using your PayPal Business Debit Mastercard.  Here are details for the recent payment made using your card ending in 1712.";
		$sample = "You paid ";
		
		//echo '<br/>' . $val . '<br/>pos=' . $pos; die;
		if ($pos !== false /* && $pos == 44 */) 
		{
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
						
			$pos = strpos($body_raw, $sample);
			if ($pos === false)
			{
				echo $body_raw . '<br/>';
				die('parse: info text not found in body raw');
			}
			if ($debug) dump($pos);
			
			//echo 'pos=' . $pos . '<br/>';die;
						
			$body_raw = substr($body_raw, $pos);
			
			// get the amount
			$amount = $this->parseTag($body_raw, 'You paid ', 6, 0); 
			$amount = floatval(trim($amount, '$'));
			$amount = -$amount;
			if ($debug) dump($amount);
					
			// get the account number, last four digits
			$account = $this->parseTag($body_raw, 'card ending in ', 4, -1); 
			$accountId = $this->getAccountId($account);
			if ($debug) dump($account);
			if ($debug) dump($accountId);
			if (!$accountId)
			{
				dump($account);
				dump($body_raw);
				dd($accountId);
			}
	
			// get the date
			$date_raw = $this->parseTag($body_raw, 'Transaction ID: ', 40, -1);
					
			$matches = [];
			// string looks like this: '| some text |'
			preg_match('/\| .* \|/', $date_raw, $matches, PREG_OFFSET_CAPTURE);
			//dump($matches);
			$date2 = count($matches) > 0 ? trim(trim($matches[0][0], '|')) : '';
			if ($debug) dump($date2);
	
			$date = new DateTime($date2);
			if ($date == NULL)
			{
				dump($body_raw);
				die("Date conversion 4 failed, from text: " . $date2);
			}
					
			// get the description
			$desc = $this->parseTag($body_raw, 'Paid to: ', 100, -1); 
			// string looks like this: '| some text |'
			preg_match('/\| .* \|/', $desc, $matches, PREG_OFFSET_CAPTURE);
			//dump($matches);
			$desc = count($matches) > 0 ? trim(trim($matches[0][0], '|')) : '';			
			if ($debug) dump($desc);
			
			if ($debug)
			{
				echo 'pos=' . $pos . ', val=' . $val . '<br/>';
				echo 'body=' . $body_raw . '<br/>';
				echo 'account=' . $account . '<br/>'; 
				echo 'accountId=' . $account . '<br/>'; 
				//dd('*** end of debug ***');
			}
			
			//dump($body_raw);												

			//echo 'Record::' . $account . '::' . $amount . '::' . $date->format('Y-m-d') . '::' . $desc . '<BR/>';
		}
		
		return $rc;
	}
		
	private function parseTag($text, $tag, $length, $wordIndex) 
	{
		$pos = strpos($text, $tag);
		$target = substr($text, $pos + strlen($tag), $length);
		//debug($target);
		if ($wordIndex >= 0)
		{
			$words = explode(" ", $target);	
			if (count($words) > $wordIndex)
			{
				$target = $words[$wordIndex];
			}
			else
			{
				dump($words);
				dd('parseTag() word index out of range: ' . $wordIndex . ', max index is: ' . (count($words) - 1));
			}
		}
		
		return $target;
	}
	
    private function addTransaction($date, $amount, $desc, $accountId, $debug) 
	{ 	
		if (!$this->isAdmin())
             return redirect('/');
           			
		$record = new Transaction();
				
		$record->user_id = Auth::id();	
		if ($record->user_id == null)
			throw new \Exception('User ID not set');
			
		$record->transaction_date	= $date->format('Y-m-d');
		$record->amount				= floatval($amount);
		$record->reconciled_flag 	= 1;
			
		// remove all non-alphas from desc
		$desc = preg_replace("/(\W)+/", " ", $desc);
		$desc = trim($desc);
		$words = explode(" ", $desc);
	
		// check for first transaction from this vendor
		if (count($words) > 2)
		{
			$vendor = $words[0] . ' ' . $words[1]; // use the first 2 words of the vendor_memo
		}
		else if (count($words) > 0)
		{
			$vendor = $words[0]; // only use the first word of the desc as the vendor_memo
		}
		else
		{
			$vendor = $desc; // no words
		}

		$trx = Transaction::getByVendor($vendor);

		if ($debug)
		{
			dump('words:');
			dump($words);
			dump('desc=' . $desc);
			dump('vendor=' . $vendor);
			dump('trx: ');
			dump($trx);
		}

		// set account
		$record->parent_id = intval($accountId);

		// copy from first transaction from this vendor
		if ($trx != null)
		{		
			$record->subcategory_id		= intval($trx->subcategory_id);
			$record->category_id		= intval($trx->category_id);
			$record->description		= $this->trimNull($trx->description);
			$record->type_flag 			= $trx->type_flag;
			//$record->vendor_memo - don't copy vendor memo because we only need one record to copy for a vendor
		}
		else // create first record from this vendor, using defaults
		{			
			// default to food::unknown
			$record->subcategory_id		= 208;  // unknown
			$record->category_id		= 2;	// food
			$record->description		= ucwords(strtolower($desc)); // uppercase the first word only
//old way:	$record->description		= ucfirst(strtolower(strtok($desc, " "))); // uppercase the first word and use it as the description
			$record->vendor_memo		= $vendor;
			$record->type_flag 			= 1;			
		}

		$record->save();

		return true;
    }	
		
}
