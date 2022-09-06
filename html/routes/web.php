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
Route::get('login/tpedu', 'App\Http\Controllers\Auth\TpeduController@redirect');
Route::get('login/tpedu/callback', 'App\Http\Controllers\Auth\TpeduController@handleCallback');
Route::get('login/{provider}', 'App\Http\Controllers\Auth\SocialiteController@redirect');
Route::get('login/{provider}/callback', 'App\Http\Controllers\Auth\SocialiteController@handleCallback');
Route::get('socialite', 'App\Http\Controllers\Auth\SocialiteController@socialite')->middleware('auth')->name('social');
Route::post('socialite/remove', 'App\Http\Controllers\Auth\SocialiteController@socialite')->middleware('auth')->name('social.remove');

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

// User Interface Routes...
Route::post('news/{id}/subscriber', 'SubscriberController@store')->name('store');
Route::get('news/{id}/delete', 'SubscriberController@delete')->name('delete');
Route::get('news/verify/{id}/{hash}', 'SubscriberController@verify')->name('verify');

Route::get('calendar', 'App\Http\Controllers\CalendarController@calendar')->name('calendar');
Route::get('calendar/event/add', 'App\Http\Controllers\CalendarController@eventAdd');
Route::post('calendar/event/add', 'App\Http\Controllers\CalendarController@eventInsert')->name('calendar.addEvent');
Route::get('calendar/event/edit/{event}', 'App\Http\Controllers\CalendarController@calendar');
Route::post('calendar/event/edit/{event}', 'App\Http\Controllers\CalendarController@calendar')->name('calendar.editEvent');
Route::get('calendar/event/remove/{event}', 'App\Http\Controllers\CalendarController@calendar')->name('calendar.removeEvent');
Route::get('calendar/seme', 'App\Http\Controllers\CalendarController@seme')->name('calendar.seme');
Route::get('calendar/training', 'App\Http\Controllers\CalendarController@training')->name('calendar.training');
Route::get('calendar/student', 'App\Http\Controllers\CalendarController@student')->name('calendar.student');
Route::get('calendar/download', 'App\Http\Controllers\CalendarController@student')->name('calendar.download');
Route::get('calendar/import', 'App\Http\Controllers\CalendarController@student')->name('calendar.import');

// Administrator Interface Routes...
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', 'App\Http\Controllers\Admin\AdminController@index')->name('admin');
    Route::get('database/sync', 'App\Http\Controllers\Admin\AdminController@syncFromTpedu');
    Route::post('database/sync', 'App\Http\Controllers\Admin\AdminController@startSyncFromTpedu')->name('sync');
    Route::get('database/sync/ad', 'App\Http\Controllers\Admin\AdminController@syncToAD');
    Route::post('database/sync/ad', 'App\Http\Controllers\Admin\AdminController@startSyncToAD')->name('syncAD');
    Route::get('database/sync/google', 'App\Http\Controllers\Admin\AdminController@syncToGsuite');
    Route::post('database/sync/google', 'App\Http\Controllers\Admin\AdminController@startSyncToGsuite')->name('syncGsuite');
    Route::get('database/units', 'App\Http\Controllers\Admin\AdminController@unitList');
    Route::post('database/units', 'App\Http\Controllers\Admin\AdminController@unitUpdate')->name('units');
    Route::get('database/units/add', 'App\Http\Controllers\Admin\AdminController@unitAdd');
    Route::post('database/units/add', 'App\Http\Controllers\Admin\AdminController@unitInsert')->name('units.add');
    Route::get('database/units/role/add', 'App\Http\Controllers\Admin\AdminController@roleAdd');
    Route::post('database/units/role/add', 'App\Http\Controllers\Admin\AdminController@roleInsert')->name('roles.add');
    Route::get('database/classes', 'App\Http\Controllers\Admin\AdminController@classList');
    Route::post('database/classes', 'App\Http\Controllers\Admin\AdminController@classUpdate')->name('classes');
    Route::get('database/subjects', 'App\Http\Controllers\Admin\AdminController@subjectList');
    Route::post('database/subjects', 'App\Http\Controllers\Admin\AdminController@subjectUpdate')->name('subjects');
    Route::get('database/teachers/{unit?}', 'App\Http\Controllers\Admin\AdminController@teacherList')->name('teachers');
    Route::get('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\AdminController@teacherEdit');
    Route::post('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\AdminController@teacherUpdate')->name('teachers.edit');
    Route::get('database/students/{myclass?}', 'App\Http\Controllers\Admin\AdminController@studentList')->name('students');
    Route::get('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\AdminController@studentEdit');
    Route::post('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\AdminController@studentUpdate')->name('students.edit');

    Route::get('website/menus/{menu?}', 'App\Http\Controllers\Admin\AdminController@menuList');
    Route::post('website/menus/{menu?}', 'App\Http\Controllers\Admin\AdminController@menuUpdate')->name('menus');
    Route::get('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\AdminController@menuAdd');
    Route::post('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\AdminController@menuInsert')->name('menus.add');
    Route::get('website/menus/remove/{menu}', 'App\Http\Controllers\Admin\AdminController@menuDelete')->name('menus.remove');
    Route::get('website/permission', 'App\Http\Controllers\Admin\AdminController@permissionList')->name('permission');
    Route::get('website/permission/add', 'App\Http\Controllers\Admin\AdminController@permissionAdd');
    Route::post('website/permission/add', 'App\Http\Controllers\Admin\AdminController@permissionInsert')->name('permission.add');
    Route::get('website/permission/{id}/edit', 'App\Http\Controllers\Admin\AdminController@permissionEdit');
    Route::post('website/permission/{id}/edit', 'App\Http\Controllers\Admin\AdminController@permissionUpdate')->name('permission.edit');
    Route::get('website/permission/{id}/remove', 'App\Http\Controllers\Admin\AdminController@permissionRemove')->name('permission.remove');
    Route::get('website/permission/{id}/grant', 'App\Http\Controllers\Admin\AdminController@grantList');
    Route::post('website/permission/{id}/grant', 'App\Http\Controllers\Admin\AdminController@grantUpdate')->name('permission.grant');
});

