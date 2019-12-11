<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Visitor;
use App\Event;
use App\Tools;

class VisitorController extends Controller
{
    public function setLocation(Request $request, Visitor $visitor)
    {    	
		if (!$this->isAdmin())
             return redirect('/');

		$record = $visitor;
		//dump($record);
		
		$geo = Tools::getIpInfo($record->ip_address);
		//dump($geo);
		
		if (isset($geo))
		{
			$record->country = $geo['country'];
			$record->countryCode = $geo['countryCode'];
			$record->city = $geo['city'];
		
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL_VISITORS, $record->country, $record->id);			
			
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Location has been updated: ' . $record->city . ' ' . $record->country);
			}
			catch (\Exception $e) 
			{
				Event::logException(LOG_MODEL_VISITORS, LOG_ACTION_EDIT, 'set location', null, $e->getMessage());
			
				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());		
			}
		}	
		
        return redirect('/visitors');
    }
}
