<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TranslationController extends Controller
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
	}	
	
}
