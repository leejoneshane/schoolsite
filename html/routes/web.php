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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes([ 'register' => false ]);

Route::get('login/tpedu', 'TpeduController@redirect');
Route::get('login/tpedu/callback', 'TpeduController@handleCallback');
Route::get('login/{provider}', 'SocialiteController@redirect');
Route::get('login/{provider}/callback', 'SocialiteController@handleCallback');
