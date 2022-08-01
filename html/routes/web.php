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

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home');

// Login Routes...
Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->middleware('firsttime')->name('login');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
Route::get('login/tpedu', 'App\Http\Controllers\TpeduController@redirect');
Route::get('login/tpedu/callback', 'App\Http\Controllers\TpeduController@handleCallback');
Route::get('login/{provider}', 'App\Http\Controllers\SocialiteController@redirect');
Route::get('login/{provider}/callback', 'App\Http\Controllers\SocialiteController@handleCallback');

// Logout Routes...
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->middleware('nouser')->name('register');
Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register')->middleware('nouser');

// Password Reset Routes...
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

// Password Confirmation Routes...
Route::get('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@confirm');

// Email Verification Routes...
Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

// Administrator Interface Routes...
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', 'App\Http\Controllers\Admin\AdminController@index')->name('admin');
    Route::get('sync', 'App\Http\Controllers\Admin\AdminController@syncFromTpedu')->name('sync');
});

