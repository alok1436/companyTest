<?php
use Illuminate\Support\Facades\Route;
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
// clear chache route
Route::get('/clear-cache', function() {
   $exitCode    = Artisan::call('cache:clear');
   $config      = Artisan::call('config:cache');
   $view        = Artisan::call('view:clear');
   return "Cache is cleared";
});

Route::get('/', function () {
    return redirect(route('login'));
});
Auth::routes();

/********************
* ADMIN ROLE ROUTES *
*********************/
Route::group(['middleware' => ['web']], function () {
	Route::middleware(['auth','admin'])->namespace('Admin')->prefix('admin')->group(function(){
		
		//Dashboard Routs
	  	Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
	  
	  	//Students Routs
		Route::match(['get','post'],'students', 'StudentController@index');
		Route::match(['get','post'],'student/store', 'StudentController@store');
		Route::match(['get','post'],'student/edit/{id}', 'StudentController@edit');

		//Classes Routs
		Route::match(['get','post'],'classes', 'ClassController@index');
		Route::match(['get','post'],'class/store', 'ClassController@store');
		Route::match(['get','post'],'class/edit/{id}', 'ClassController@edit');
		Route::match(['get','post'],'class/delete/{id}', 'ClassController@delete');

	});
});


/********************
* TEACHER ROLE ROUTES *
*********************/
Route::group(['middleware' => ['web']], function () {
	Route::middleware(['auth','teacher'])->namespace('Teacher')->prefix('teacher')->group(function(){

		//Dashboard Tests Routs
		Route::match(['get','post'],'dashboard', 'DashboardController@index')->name('teacher.dashboard');

	});
});

/****************************
* STUDENT ROUTES *
*****************************/
Route::middleware(['auth','student'])->namespace('Student')->prefix('student')->group(function(){

	//Dashboard Routs
	Route::match(['get','post'],'dashboard', 'DashboardController@index')->name('student.dashboard');
});

Route::match(['get','post'],'logout', 'Auth\LoginController@logout');