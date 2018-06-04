<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//sbw, call a view directly
//Route::get('/', function () { return view('welcome'); });

Auth::routes();

// public pages
Route::get('/', 'HomeController@index');
Route::get('/about', 'HomeController@about');
Route::get('/view/{entry}', 'HomeController@view');
Route::get('/tours', 'HomeController@tours');
Route::get('/posts', 'HomeController@posts');
Route::get('/visits', 'HomeController@visits')->middleware('auth');;
Route::get('/visitors/{sort?}', 'HomeController@visitors')->middleware('auth');;
Route::get('/admin', 'HomeController@admin')->middleware('auth');
Route::get('/home', 'HomeController@index');

// crypt / encrypt
Route::get('/hash', 'EntryController@hash')->middleware('auth');
Route::post('/hasher', 'EntryController@hasher')->middleware('auth');

// this is he new front page to replace the HomeController
Route::group(['prefix' => 'frontpage'], function () {
	
	Route::get('/index', 'FrontPageController@index');
	Route::get('/visitors/{sort?}', 'FrontPageController@visitors');
	Route::get('/admin', 'FrontPageController@admin');

});

// tours is a superclass of entries, uses entries for basic functions
Route::group(['prefix' => 'tours'], function () {

	// list
	Route::get('/index', 'TourController@index')->middleware('auth');
	Route::get('/indexadmin', 'TourController@indexadmin')->middleware('auth');
	Route::get('/location{id}', 'TourController@location');

	// view
	Route::get('/view/{title}/{id}', ['as' => 'tour.view', 'uses' => 'TourController@view']);
	Route::resource('tour', 'TourController');	
	
	// publish
	Route::get('/publish/{entry}', 'EntryController@publish')->middleware('auth');
	Route::post('/publishupdate/{entry}', 'EntryController@publishupdate')->middleware('auth');
	
	// add/create
	Route::get('/add','TourController@add')->middleware('auth');
	Route::post('/create','TourController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{id}','TourController@edit')->middleware('auth');
	Route::post('/update/{entry}','TourController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}', 'TourController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}', 'TourController@delete')->middleware('auth');

});

Route::group(['prefix' => 'entries'], function () {
	
	Route::get('/tours', 'EntryController@tours')->middleware('auth');
	Route::get('/posts', 'EntryController@posts')->middleware('auth');
	Route::get('/index', 'EntryController@index')->middleware('auth');
	Route::get('/indexadmin', 'EntryController@indexadmin')->middleware('auth');
	Route::get('/tag/{tag_id}', 'EntryController@tag')->middleware('auth');

	// view
	Route::get('/show/{id}', ['as' => 'entry.view', 'uses' => 'EntryController@show']);
	Route::get('/view/{title}/{id}', ['as' => 'entry.view', 'uses' => 'EntryController@view']);
	Route::resource('entry', 'EntryController');	
	
	// publish
	Route::get('/publish/{entry}', 'EntryController@publish')->middleware('auth');
	Route::post('/publishupdate/{entry}', 'EntryController@publishupdate')->middleware('auth');
	
	// add/create
	Route::get('/add','EntryController@add')->middleware('auth');
	Route::post('/create','EntryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{entry}','EntryController@edit')->middleware('auth');
	Route::post('/update/{entry}','EntryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}','EntryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}','EntryController@delete')->middleware('auth');	
});

Route::group(['prefix' => 'locations'], function () 
{
	Route::get('/activities/{location?}', 'LocationsController@activities');
	Route::get('/index', 'LocationsController@index');
	
	Route::get('/', 'LocationsController@index')->middleware('auth');
	Route::get('/indexadmin', 'LocationsController@indexadmin')->middleware('auth');
	Route::get('/view/{location}','LocationsController@view')->middleware('auth');

	// add/create
	Route::get('/add','LocationsController@add')->middleware('auth');
	Route::post('/create','LocationsController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{location}','LocationsController@edit')->middleware('auth');
	Route::post('/update/{location}','LocationsController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{location}','LocationsController@confirmdelete')->middleware('auth');
	Route::post('/delete/{location}','LocationsController@delete')->middleware('auth');
});

Route::group(['prefix' => 'activities'], function () 
{
	// index
	Route::get('/index', 'ActivityController@index');
	Route::get('/maps', 'ActivityController@maps');
	Route::get('/indexadmin', 'ActivityController@indexadmin')->middleware('auth');
	
	Route::get('/view/{title}/{id}', ['as' => 'activity.view', 'uses' => 'ActivityController@view']);
	Route::resource('activity', 'ActivityController');	
	//orig: Route::get('/view/{activity}', 'ActivityController@view');

	// location
	Route::get('/location/{activity}','ActivityController@location')->middleware('auth');
	Route::post('/locationupdate/{activity}','ActivityController@locationupdate')->middleware('auth');
	
	// publish
	Route::get('/publish/{activity}','ActivityController@publish')->middleware('auth');
	Route::post('/publishupdate/{activity}','ActivityController@publishupdate')->middleware('auth');
	
	// add/create
	Route::get('/add','ActivityController@add')->middleware('auth');
	Route::post('/create','ActivityController@create')->middleware('auth');
	
	// edit/update
	Route::get('/edit/{activity}','ActivityController@edit')->middleware('auth');
	Route::post('/update/{activity}','ActivityController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{activity}','ActivityController@confirmdelete')->middleware('auth');
	Route::post('/delete/{activity}','ActivityController@delete')->middleware('auth');
});

Route::group(['prefix' => 'photos'], function () 
{
	// index
	Route::get('/index', 'PhotoController@index')->middleware('auth');
	Route::get('/indexadmin', 'PhotoController@indexadmin')->middleware('auth');
	Route::get('/sliders', 'PhotoController@sliders');
	Route::get('/tours/{id}', 'PhotoController@tours')->middleware('auth');
	Route::get('/entries/{id}', 'PhotoController@entries')->middleware('auth');
	Route::get('/view/{photo}', 'PhotoController@view');

	// add/create
	Route::get('/add/{parent_id?}','PhotoController@add')->middleware('auth');
	Route::post('/create','PhotoController@create')->middleware('auth');
	
	// edit/update
	Route::get('/edit/{photo}','PhotoController@edit')->middleware('auth');
	Route::post('/update/{photo}','PhotoController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{photo}','PhotoController@confirmdelete')->middleware('auth');
	Route::post('/delete/{photo}','PhotoController@delete')->middleware('auth');
});

Route::group(['prefix' => 'faqs'], function () {
	Route::get('/', 'FaqsController@index')->middleware('auth');
	Route::get('/index', 'FaqsController@index')->middleware('auth');
	Route::get('/view/{faq}','FaqsController@view')->middleware('auth');
	Route::get('/search/{entry}','FaqsController@search')->middleware('auth');

	// add/create
	Route::get('/add','FaqsController@add')->middleware('auth');
	Route::post('/create','FaqsController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{faq}','FaqsController@edit')->middleware('auth');
	Route::post('/update/{faq}','FaqsController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{faq}','FaqsController@confirmdelete')->middleware('auth');
	Route::post('/delete/{faq}','FaqsController@delete')->middleware('auth');
});

Route::group(['prefix' => 'tags'], function () {
	Route::get('/', 'TagsController@index')->middleware('auth');
	Route::get('/index', 'TagsController@index')->middleware('auth');
	Route::get('/entries/{tag}', 'TagsController@entries')->middleware('auth');

	// add/create
	Route::get('/add','TagsController@add')->middleware('auth');
	Route::post('/create','TagsController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{task}','TagsController@edit')->middleware('auth');
	Route::post('/update/{task}','TagsController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{task}','TagsController@confirmdelete')->middleware('auth');
	Route::post('/delete/{task}','TagsController@delete')->middleware('auth');
});

Route::group(['prefix' => 'tasks'], function () {
	
	Route::get('/', 'TasksController@index')->middleware('auth');
	Route::get('/index', 'TasksController@index')->middleware('auth');

	// add/create
	Route::get('/add','TasksController@add')->middleware('auth');
	Route::post('/create','TasksController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{task}','TasksController@edit')->middleware('auth');
	Route::post('/update/{task}','TasksController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{task}','TasksController@confirmdelete')->middleware('auth');
	Route::post('/delete/{task}','TasksController@delete')->middleware('auth');
});

Route::group(['prefix' => 'users'], function () {
	Route::get('/', 'UsersController@index')->middleware('auth');
	Route::get('/index', 'UsersController@index')->middleware('auth');
	Route::get('/view/{user}','UsersController@view')->middleware('auth');

	// add/create
	Route::get('/add','UsersController@add')->middleware('auth');
	Route::post('/create','UsersController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{user}','UsersController@edit')->middleware('auth');
	Route::post('/update/{user}','UsersController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{user}','UsersController@confirmdelete')->middleware('auth');
	Route::post('/delete/{user}','UsersController@delete')->middleware('auth');
});


