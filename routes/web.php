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

// crypt / encrypt
Route::get('/hash', 'EntryController@hash')->middleware('auth');
Route::post('/hasher', 'EntryController@hasher')->middleware('auth');

Route::group(['prefix' => 'photos'], function () 
{
	// index
	Route::get('/index', 'PhotoController@index')->middleware('auth');
	Route::get('/sliders', 'PhotoController@sliders')->middleware('auth');
	Route::get('/tours/{id}', 'PhotoController@tours')->middleware('auth');

	// add/create
	Route::get('/add/{id}','PhotoController@add')->middleware('auth');
	Route::post('/create/{id}','PhotoController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/','PhotoController@edit')->middleware('auth');
	Route::post('/update/{entry}','PhotoController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/','PhotoController@confirmdelete')->middleware('auth');
	Route::post('/delete/','PhotoController@delete')->middleware('auth');
});

Route::group(['prefix' => 'entries'], function () {
	
	Route::get('/tours', 'EntryController@tours')->middleware('auth');
	Route::get('/posts', 'EntryController@posts')->middleware('auth');
	Route::get('/index', 'EntryController@index')->middleware('auth');

	// photo upload/store
	Route::get('/upload/{entry}','EntryController@upload')->middleware('auth');
	Route::post('/store/{entry}','EntryController@store')->middleware('auth');
	
	// add/create
	Route::get('/add','EntryController@add')->middleware('auth');
	Route::post('/create','EntryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{entry}','EntryController@edit')->middleware('auth');
	Route::post('/update/{entry}','EntryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}','EntryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}','EntryController@delete')->middleware('auth');
	
	// other gets
	Route::get('/viewcount/{entry}','EntryController@viewcount')->middleware('auth');
	Route::get('/view/{entry}','EntryController@view')->middleware('auth');
	Route::get('/gen/{entry}','EntryController@gen')->middleware('auth');
	Route::get('/search/{entry}','EntryController@search')->middleware('auth');
	Route::get('/gendex/{id?}','EntryController@gendex')->middleware('auth');
	Route::get('/settemplate/{id}','EntryController@settemplate')->middleware('auth');
	Route::get('/timer', 'EntryController@timer')->middleware('auth');
	
	// other posts
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


