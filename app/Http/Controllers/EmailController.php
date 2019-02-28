<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Event;
use Auth;
use App\Transaction;

define('LOG_MODEL', 'email');
define('TITLE', 'Email');

// chase
define("EMAIL_CHASE_ID", 35);
define("EMAIL_CHASE_ACCOUNT", '2117');

// cap gray
define("EMAIL_CAPGRAY_ID", 31);
define("EMAIL_CAPGRAY_ACCOUNT", '6403');

// cap blue
define("EMAIL_CAPBLUE_ID", 10);  
define("EMAIL_CAPBLUE_ACCOUNT", '5043');

class EmailController extends Controller
{	
	private function getAccountId($account)
	{
		$accountId = EMAIL_CAPGRAY_ID;

		if ($account === EMAIL_CHASE_ACCOUNT)
			$accountId = EMAIL_CHASE_ID;
		else if ($account === EMAIL_CAPGRAY_ACCOUNT)
			$accountId = EMAIL_CAPGRAY_ID;
		else if ($account === EMAIL_CAPBLUE_ACCOUNT)
			$accountId = EMAIL_CAPBLUE_ID;

		return $accountId;
	}	

    public function check(Request $request, $debug = false) 
	{		
		$email_account = env('EMAIL_USERNAME');
		$email_password = env('EMAIL_PASSWORD');
		$email_server = env('EMAIL_HOST');
		$email_port = env('EMAIL_PORT');
		$email_driver = env('EMAIL_DRIVER');
		$email_encryption = env('EMAIL_ENCRYPTION');

		if ($email_account == 'spam@scotthub.com')
		{
			$debug = true;
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
			die;
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
		$subject = 'A new transaction was';
					
		$pos = strpos($val, $subject);
										
		$sample = "A purchase was charged to your account. RE: Account ending in 6789 SCOTT, As requested, we're notifying you that on SEP 30, 2016, at USA*CANTEEN VENDING, a pending authorization or purchase in the amount of $1.00 was placed or charged on your Capital One VISA SIGNATURE account.";
				
		if ($pos !== false && $pos == 44) 
		{
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
						
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
						
			// date looks like: SEP 30, 2016
			$date_raw = $this->parseTag($body_raw, 'notifying you that on ', 12, -1); 
			$date2 = str_replace(',', '', $date_raw);
			$date = DateTime::createFromFormat('M d Y', $date2);
			if ($date == NULL)
			{
				// try this format: 11/08/2018
				$date_raw = $this->parseTag($body_raw, 'notifying you that on ', 10, -1); 
				$date = DateTime::createFromFormat('m/d/Y', $date_raw);
				if ($date == NULL)
				{
					die("Date conversion 2 failed, from text: " . $date_raw);
				}
			}
									
			// get the account number, last four digits
			$account = $this->parseTag($body_full, 'RE: Account ending in ', 4, -1); 
			$accountId = $this->getAccountId($account);

			// get the description
			$desc = $this->parseTag($body_raw, $date_raw . ', at ', 30, -1); 
			$pieces = explode(',', $desc);
			$desc = $pieces[0];
						
			if ($debug)
			{
				echo 'pos=' . $pos . ', val=' . $val . '<br/>';
				echo 'body=' . $body_raw . '<br/>';
				echo 'account=' . $account . '<br/>'; 
				echo 'accountId=' . $account . '<br/>'; 
				echo 'desc=' . $desc . '<br/>'; 
				//die('*** end of debug ***');
			}
			
			//echo 'Record:' . $account_raw . '::' . $amount . '::' . $date->format('Y-m-d') . '::' . $desc;
		}
		
		return $rc;
	}
	
	private function checkChase($mbox, $count, $val, &$date, &$amount, &$desc, &$accountId, $debug) 
	{
		$rc = false; 
		$subject = 'Your Single Transaction';
					
		$pos = strpos($val, $subject);
					
		$sample = "This is an Alert to help you manage your credit card account ending in 2117.  As you requested, we are notifying you of any charges over the amount of (\$USD) 0.01, as specified in your Alert settings. A charge of (\$USD) 80.20 at WAL-MART #2516 has been authorized on 04/03/2017 11:02:20 PM EDT.";

		//echo '<br/>' . $val . '<br/>pos=' . $pos; die;
		if ($pos !== false && $pos == 44) 
		{
			$rc = true; // transaction found
			//echo 'pos = ' . $pos . '<br/>';
						
			// get the body
			$body_raw = imap_body($mbox, $count);
						
			$pos = strpos($body_raw, substr($sample, 0, 76));
			if ($pos === false)
			{
				echo $body_raw . '<br/>';
				die('parse: info text not found in body raw');
			}
			//echo 'pos=' . $pos . '<br/>';die;
						
			$body_raw = substr($body_raw, $pos, strlen($sample));
												
			// get the amount
			$amount = $this->parseTag($body_raw, 'A charge of ($USD) ', 10, 0); 
			$amount = floatval(trim($amount, '$'));
			$amount = -$amount;
						
			// get the date
			$date_raw = $this->parseTag($body_raw, 'has been authorized on ', 10, -1); 
			$date2 = str_replace(',', '', $date_raw);
			//echo '|' . $date2 . '|';
			$date = DateTime::createFromFormat('m/d/Y', $date2);
			//debug($date);die;
			if ($date == NULL)
			{
				die("Date conversion 3 failed, from text: " . $date2);
			}
									
			// get the account number, last four digits
			$account = $this->parseTag($body_raw, 'account ending in ', 4, -1); 
			$accountId = $this->getAccountId($account);

			// get the description
			$desc = $this->parseTag($body_raw, 'A charge of ($USD) ', 30, -1); 
			$pieces = explode(' ', $desc);
			//debug($pieces);
			$desc = $pieces[2];
			if ($pieces[3] !== 'has')
				$desc .= ' ' . $pieces[3];
						
			if ($debug)
			{
				echo 'pos=' . $pos . ', val=' . $val . '<br/>';
				echo 'body=' . $body_raw . '<br/>';
				echo 'account=' . $account . '<br/>'; 
				echo 'accountId=' . $account . '<br/>'; 
				//die('*** end of debug ***');
			}

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
			$target = $words[$wordIndex];
		}
		
		return $target;
	}
	
    private function addTransaction($date, $amount, $desc, $accountId, $debug) 
	{ 	
		if (!$this->isAdmin())
             return redirect('/');
           			
		$record = new Transaction();
				
		$record->user_id = Auth::id();	
		$record->transaction_date	= $date->format('Y-m-d');
		$record->amount				= floatval($amount);
		$record->reconciled_flag 	= 1;
			
		// remove all non-alphas from desc
		$desc = preg_replace("/(\W)+/", " ", $desc);
		
		// check for first transaction from this vendor
		$vendor = $desc; // use the full desc as the vendor_memo
		$trx = Transaction::getByVendor($vendor);

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
			// default to food::groceries
			$record->subcategory_id		= 208; // unknown, orig: 102;
			$record->category_id		= 2;
			$record->description		= ucfirst(strtolower(strtok($desc, " "))); // only use the first word as the description
			$record->vendor_memo		= $vendor;
			$record->type_flag 			= 1;			
		}

		$record->save();

		return true;
    }	
		
}
