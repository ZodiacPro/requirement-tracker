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

Auth::routes();

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home')->middleware('auth');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
});

Route::group(['middleware' => 'auth'], function () {
	Route::get('site', ['as' => 'site.list', 'uses' => 'App\Http\Controllers\SiteController@list']);
	Route::post('site', ['as' => 'site.list', 'uses' => 'App\Http\Controllers\SiteController@list']);
	Route::post('site-create', ['as' => 'site.create', 'uses' => 'App\Http\Controllers\SiteController@create']);
	Route::get('detail/{id}', ['as' => 'site.detail', 'uses' => 'App\Http\Controllers\SiteController@detail']);
	Route::post('update/{id}', ['as' => 'site.update', 'uses' => 'App\Http\Controllers\SiteController@update']);
	Route::post('step-create/{id}', ['as' => 'step.create', 'uses' => 'App\Http\Controllers\SiteController@add_step']);
	Route::post('step-task', ['as' => 'task.create', 'uses' => 'App\Http\Controllers\SiteController@add_task']);
	Route::post('step-item', ['as' => 'item.create', 'uses' => 'App\Http\Controllers\SiteController@add_item']);
	Route::post('remarks/{id}', [App\Http\Controllers\SiteController::class, 'remarks'])->name('remarks');
	Route::get('aprrove/{id}', [App\Http\Controllers\SiteController::class, 'aprrove'])->name('aprrove');
	Route::get('reject/{id}', [App\Http\Controllers\SiteController::class, 'reject'])->name('reject');
	Route::post('step-item-delete', ['as' => 'item.delete', 'uses' => 'App\Http\Controllers\SiteController@delete_item']);
	Route::post('step-task-delete', ['as' => 'task.delete', 'uses' => 'App\Http\Controllers\SiteController@delete_task']);
	Route::post('step-delete', ['as' => 'step.delete', 'uses' => 'App\Http\Controllers\SiteController@delete_step']);
	Route::get('auto/{id}', [App\Http\Controllers\SiteController::class, 'auto_add'])->name('auto');
});

Route::group(['middleware' => 'auth'], function () {
	Route::get('area', ['as' => 'area.list', 'uses' => 'App\Http\Controllers\AreaController@list']);
	Route::post('area', ['as' => 'area.list', 'uses' => 'App\Http\Controllers\AreaController@list']);
	Route::post('area-create', ['as' => 'area.create', 'uses' => 'App\Http\Controllers\AreaController@create']);
});


Route::group(['middleware' => 'auth'], function () {
	Route::get('data/export/{id}', [App\Http\Controllers\SiteController::class, 'export'])->name('dl.all');
	Route::get('data/approve/{id}', [App\Http\Controllers\SiteController::class, 'approved'])->name('dl.approve');
	Route::get('data/reject/{id}', [App\Http\Controllers\SiteController::class, 'rejected'])->name('dl.reject');
	Route::get('data/textall/{id}', [App\Http\Controllers\SiteController::class, 'textall'])->name('dl.textall');
	Route::get('data/textapprove/{id}', [App\Http\Controllers\SiteController::class, 'textapprove'])->name('dl.textapprove');
	Route::get('data/textreject/{id}', [App\Http\Controllers\SiteController::class, 'textreject'])->name('dl.textreject');
});