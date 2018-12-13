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
Route::get('/', 'FrontPageController@index');
Route::get('/about', 'FrontPageController@about');
Route::get('/view/{entry}', 'HomeController@view');
Route::get('/visits', 'FrontPageController@visits')->middleware('auth');;
Route::get('/visitors/{sort?}', 'FrontPageController@visitors')->middleware('auth');;
Route::get('/admin', 'FrontPageController@admin')->middleware('auth');
Route::get('/home', 'HomeController@index');
Route::get('/error', 'FrontPageController@error');
Route::get('/travelocity', 'FrontPageController@travelocity');
Route::get('/expedia', 'FrontPageController@expedia');
Route::get('/email/check', 'EmailController@check');
Route::get('/articles', 'EntryController@articles');
Route::get('/confirm', 'FrontPageController@confirm');
Route::get('/spy', 'FrontPageController@spy');
Route::get('/spyoff', 'FrontPageController@spyoff');
Route::get('/gallery', 'EntryController@gallery');
Route::get('/first', 'FrontPageController@first');
Route::get('/search', 'ToolController@search');
Route::post('/search', 'ToolController@search');
Route::get('/sitemap', 'ToolController@sitemap')->middleware('auth');
Route::get('/test', 'ToolController@test')->middleware('auth');
Route::post('/test', 'ToolController@test')->middleware('auth');


// crypt / encrypt
Route::get('/hash', 'EntryController@hash')->middleware('auth');
Route::post('/hasher', 'EntryController@hasher')->middleware('auth');

// sections
Route::group(['prefix' => 'sections'], function () {
	
	Route::get('/', 'SectionController@index')->middleware('auth');
	Route::get('/view/{entry}', 'SectionController@view')->middleware('auth');
	Route::get('/show/{entry}', 'SectionController@show')->middleware('auth');
		
	// publish
	Route::get('/publish/{entry}', 'SectionController@publish')->middleware('auth');
	Route::post('/publishupdate/{entry}', 'EntryController@publishupdate')->middleware('auth');

	// add/create
	Route::get('/add','SectionController@add')->middleware('auth');
	Route::post('/create','EntryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{entry}','SectionController@edit')->middleware('auth');
	Route::post('/update/{entry}','EntryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}','SectionController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}','EntryController@delete')->middleware('auth');		
});

// Galleries
Route::group(['prefix' => 'galleries'], function () {

	Route::get('/', 'GalleryController@index');
	Route::get('/index', 'GalleryController@index');
	Route::get('/indexadmin', 'GalleryController@indexadmin')->middleware('auth');
	Route::get('/view/{entry}', ['as' => 'entry.view', 'uses' => 'GalleryController@view']);
	Route::get('/share/{entry_id}', 'GalleryController@share')->middleware('auth');
	Route::get('/link/{entry_id}/{gallery_id}', 'GalleryController@link')->middleware('auth');
	Route::get('/attach/{entry_id}/{photo_id}', 'GalleryController@attach')->middleware('auth');
	Route::get('/setmain/{entry_id}/{photo_id}', 'GalleryController@setmain')->middleware('auth');
	
	// add/create/copy
	Route::get('/add/{entry}','GalleryController@add')->middleware('auth');
	Route::post('/create','GalleryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{entry}','GalleryController@edit')->middleware('auth');
	Route::post('/update/{entry}','GalleryController@update')->middleware('auth');
	Route::post('/move/{photo}','GalleryController@move')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}', 'GalleryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}', 'GalleryController@delete')->middleware('auth');	
	
	// permalink has to go at the bottom of the filter or it will catch everything
	Route::get('/{permalink}', ['as' => 'gallery.permalink', 'uses' => 'GalleryController@permalink']);	
});

// Transfers
Route::group(['prefix' => 'transfers'], function () {
	
	Route::get('/index', 'TransferController@index');
	Route::get('/view/{transaction}', ['as' => 'account.view', 'uses' => 'TransferController@view']);
	
	// add/create/copy
	Route::get('/copy/{transaction}','TransferController@copy')->middleware('auth');
	Route::get('/add/{account}','TransferController@add')->middleware('auth');
	Route::post('/create','TransferController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{transaction}','TransferController@edit')->middleware('auth');
	Route::post('/update/{transaction}','TransferController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{transaction}', 'TransferController@confirmdelete')->middleware('auth');
	Route::post('/delete/{transaction}', 'TransferController@delete')->middleware('auth');	
});


// Subcategories
Route::group(['prefix' => 'subcategories'], function () {
	
	Route::get('/index', 'SubcategoryController@index');
	Route::get('/indexadmin', 'SubcategoryController@indexadmin')->middleware('auth');
	Route::get('/view/{category}', ['as' => 'account.view', 'uses' => 'SubcategoryController@view']);
	
	// add/create
	Route::get('/add','SubcategoryController@add')->middleware('auth');
	Route::post('/create','SubcategoryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{category}','SubcategoryController@edit')->middleware('auth');
	Route::post('/update/{category}','SubcategoryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{category}', 'SubcategoryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{category}', 'SubcategoryController@delete')->middleware('auth');	
});

// Categories
Route::group(['prefix' => 'categories'], function () {
	
	Route::get('/subcategories/{category_id}', 'CategoryController@subcategories');
	Route::get('/index', 'CategoryController@index');
	Route::get('/indexadmin', 'CategoryController@indexadmin')->middleware('auth');
	Route::get('/view/{category}', ['as' => 'account.view', 'uses' => 'CategoryController@view']);
	
	// add/create
	Route::get('/add','CategoryController@add')->middleware('auth');
	Route::post('/create','CategoryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{category}','CategoryController@edit')->middleware('auth');
	Route::post('/update/{category}','CategoryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{category}', 'CategoryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{category}', 'CategoryController@delete')->middleware('auth');	
});

// Transactions
Route::group(['prefix' => 'transactions'], function () {

	Route::get('/show/{filter}/{id}', 'TransactionController@show')->middleware('auth');
	
	Route::get('/expenses', 'TransactionController@expenses')->middleware('auth');
	Route::post('/expenses', 'TransactionController@expenses')->middleware('auth');
	Route::get('/summary/{showAll?}', 'TransactionController@summary')->middleware('auth');
	Route::get('/index/{subcategory_id?}', 'TransactionController@indexadmin')->middleware('auth');
	Route::get('/view/{id}', ['as' => 'account.view', 'uses' => 'TransactionController@view']);

	// filter
	Route::get('/filter','TransactionController@filter')->middleware('auth');
	Route::post('/filter','TransactionController@filter')->middleware('auth');
	
	// add/create/copy/transfer
	Route::get('/copy/{transaction}','TransactionController@copy')->middleware('auth');
	Route::get('/transfer/{account}','TransactionController@transfer')->middleware('auth');
	Route::post('/transfercreate','TransactionController@transfercreate')->middleware('auth');
	Route::get('/add','TransactionController@add')->middleware('auth');
	Route::post('/create','TransactionController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{transaction}','TransactionController@edit')->middleware('auth');
	Route::post('/update/{transaction}','TransactionController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{transaction}', 'TransactionController@confirmdelete')->middleware('auth');
	Route::post('/delete/{transaction}', 'TransactionController@delete')->middleware('auth');	
});

// Accounts
Route::group(['prefix' => 'accounts'], function () {
	
	Route::get('/index/{showAll?}', 'AccountController@index')->middleware('auth');
	Route::get('/view/{account}', ['as' => 'account.view', 'uses' => 'AccountController@view']);
	
	// add/create
	Route::get('/add','AccountController@add')->middleware('auth');
	Route::post('/create','AccountController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{account}','AccountController@edit')->middleware('auth');
	Route::post('/update/{account}','AccountController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{account}', 'AccountController@confirmdelete')->middleware('auth');
	Route::post('/delete/{account}', 'AccountController@delete')->middleware('auth');	
});

// templates
Route::group(['prefix' => 'templates'], function () {
	
	Route::get('/index', 'TemplateController@index');
	Route::get('/indexadmin', 'TemplateController@indexadmin')->middleware('auth');
	Route::get('/view/{template}', ['as' => 'template.view', 'uses' => 'TemplateController@view']);
	
	// add/create
	Route::get('/add','TemplateController@add')->middleware('auth');
	Route::post('/create','TemplateController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{template}','TemplateController@edit')->middleware('auth');
	Route::post('/update/{template}','TemplateController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{template}', 'TemplateController@confirmdelete')->middleware('auth');
	Route::post('/delete/{template}', 'TemplateController@delete')->middleware('auth');
	
	// publish
	Route::get('/publish/{template}', 'TemplateController@publish')->middleware('auth');
	Route::post('/publishupdate/{template}', 'TemplateController@publishupdate')->middleware('auth');	

	// permalink has to go at the bottom of the filter or it will catch everything
	Route::get('/{permalink}', ['as' => 'template.permalink', 'uses' => 'TemplateController@permalink']);	
});

// blogs
Route::group(['prefix' => 'blogs'], function () 
{	
	Route::get('/show/{id}/{all?}', 'BlogController@show');
	Route::get('/view/{id}', 'BlogController@view');
	Route::get('/index', 'BlogController@index');
	Route::get('/indexadmin', 'BlogController@indexadmin')->middleware('auth');
	
	// add post /create post
	Route::get('/addpost/{id}', 'BlogController@addpost')->middleware('auth');
	Route::post('/create', 'EntryController@create')->middleware('auth');

	// edit post / update post
	Route::get('/editpost/{id}', 'BlogController@addpost')->middleware('auth');
	Route::post('/updatepost', 'BlogController@create')->middleware('auth');
});

// sites
Route::group(['prefix' => 'events'], function () {
	
	Route::get('/index/{type_flag?}', 'EventController@index')->middleware('auth');
});

// sites
Route::group(['prefix' => 'sites'], function () {
	
	Route::get('/index', 'SiteController@index')->middleware('auth');
	Route::get('/view/{site}', ['as' => 'entry.view', 'uses' => 'SiteController@view'])->middleware('auth');
	
	// add/create
	Route::get('/add','SiteController@add')->middleware('auth');
	Route::post('/create','SiteController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{site}','SiteController@edit')->middleware('auth');
	Route::post('/update/{site}','SiteController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{site}', 'SiteController@confirmdelete')->middleware('auth');
	Route::post('/delete/{site}', 'SiteController@delete')->middleware('auth');
	
});

// this is the new front page to replace the HomeController
Route::group(['prefix' => 'frontpage'], function () {
	
	Route::get('/index', 'FrontPageController@index');
	Route::get('/visitors', 'FrontPageController@visitors')->middleware('auth');
	Route::post('/visitors', 'FrontPageController@visitors')->middleware('auth');
	Route::get('/admin', 'FrontPageController@admin')->middleware('auth');

});

// tours is a superclass of entries, uses entries for basic functions
Route::group(['prefix' => 'tours'], function () {

	// list
	Route::get('/index', 'TourController@index');
	Route::get('/indexadmin', 'TourController@indexadmin')->middleware('auth');
	Route::get('/location/{location_id}', 'TourController@location');
	
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

	// view
	Route::get('/view/{title}/{id}', ['as' => 'tour.view', 'uses' => 'TourController@view']);
	Route::resource('tour', 'TourController');		
	Route::get('/{permalink}', ['as' => 'tour.permalink', 'uses' => 'TourController@permalink']);
	Route::get('/{location}/{permalink}', ['as' => 'tour.permalocation', 'uses' => 'TourController@permalocation']);
});

Route::group(['prefix' => 'entries'], function () {
	
	Route::get('/index', 'EntryController@index');
	Route::get('/tours', 'EntryController@tours')->middleware('auth');
	Route::get('/posts', 'EntryController@posts')->middleware('auth');
	Route::get('/indexadmin/{type_flag?}', 'EntryController@indexadmin')->middleware('auth');
	Route::get('/tag/{tag_id}', 'EntryController@tag')->middleware('auth');
	Route::get('/show/{id}', 'EntryController@show');
		
	// publish
	Route::get('/publish/{entry}', 'EntryController@publish')->middleware('auth');
	Route::post('/publishupdate/{entry}', 'EntryController@publishupdate')->middleware('auth');
	
	// location
	Route::get('/setlocation/{entry}','EntryController@setlocation')->middleware('auth');
	Route::post('/locationupdate/{entry}','EntryController@locationupdate')->middleware('auth');
	
	// add/create
	Route::get('/add','EntryController@add')->middleware('auth');
	Route::post('/create','EntryController@create')->middleware('auth');

	// edit/update
	Route::get('/edit/{entry}','EntryController@edit')->middleware('auth');
	Route::post('/update/{entry}','EntryController@update')->middleware('auth');

	// delete / confirm delete
	Route::get('/confirmdelete/{entry}','EntryController@confirmdelete')->middleware('auth');
	Route::post('/delete/{entry}','EntryController@delete')->middleware('auth');	
	
	// permalink catch alls
	Route::get('/view/{title}/{id}', ['as' => 'entry.view', 'uses' => 'EntryController@view']);
	Route::get('/{permalink}', ['as' => 'entry.permalink', 'uses' => 'EntryController@permalink']);
	Route::resource('entry', 'EntryController');		
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
	Route::get('/indexadmin', 'PhotoController@indexadmin')->middleware('auth');
	Route::get('/sliders', 'PhotoController@sliders');
	Route::get('/featured', 'PhotoController@featured');
	Route::get('/tours/{id}', 'PhotoController@tours')->middleware('auth');
	Route::get('/entries/{id}/{folder?}', 'PhotoController@entries')->middleware('auth');
	Route::get('/view/{photo}', 'PhotoController@view');
	Route::get('/slideshow/{entry}', 'PhotoController@slideshow');

	// add/create
	Route::get('/add/{type_flag}/{parent_id?}','PhotoController@add')->middleware('auth');
	Route::post('/create','PhotoController@create')->middleware('auth');
	
	// edit/update
	Route::get('/edit/{photo}','PhotoController@edit')->middleware('auth');
	Route::post('/update/{photo}','PhotoController@update')->middleware('auth');
	Route::post('/updateparent/{photo}','PhotoController@updateparent')->middleware('auth');

	// rotate
	Route::get('/rotate/{photo}','PhotoController@rotate')->middleware('auth');

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

