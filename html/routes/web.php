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
Route::post('socialite/remove', 'App\Http\Controllers\Auth\SocialiteController@removeSocialite')->middleware('auth')->name('social.remove');

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

// news letter routes...
Route::post('news/{id}/subscriber', 'SubscriberController@store')->name('store');
Route::get('news/{id}/delete', 'SubscriberController@delete')->name('delete');
Route::get('news/verify/{id}/{hash}', 'SubscriberController@verify')->name('verify');

// calendar Routes...
Route::get('calendar', 'App\Http\Controllers\CalendarController@calendar')->name('calendar');
Route::get('calendar/event/add', 'App\Http\Controllers\CalendarController@eventAdd')->middleware('auth');
Route::post('calendar/event/add', 'App\Http\Controllers\CalendarController@eventInsert')->middleware('auth')->name('calendar.addEvent');
Route::get('calendar/event/edit/{event}', 'App\Http\Controllers\CalendarController@eventEdit')->middleware('auth');
Route::post('calendar/event/edit/{event}', 'App\Http\Controllers\CalendarController@eventUpdate')->middleware('auth')->name('calendar.editEvent');
Route::post('calendar/event/remove/{event}', 'App\Http\Controllers\CalendarController@eventRemove')->middleware('auth')->name('calendar.removeEvent');
Route::get('calendar/seme', 'App\Http\Controllers\CalendarController@seme')->name('calendar.seme');
Route::get('calendar/training', 'App\Http\Controllers\CalendarController@training')->name('calendar.training');
Route::get('calendar/student', 'App\Http\Controllers\CalendarController@student')->name('calendar.student');
Route::get('calendar/download', 'App\Http\Controllers\CalendarController@student')->name('calendar.download');
Route::get('calendar/import', 'App\Http\Controllers\CalendarController@student')->name('calendar.import');

//intime messager routes...
Route::post('messager/send', 'App\Http\Controllers\MessagerController@send')->middleware('auth')->name('messager.send');

//student club routes...
Route::group(['prefix' => 'club', 'middleware' => [ 'auth' ] ], function () {
    Route::get('/', 'App\Http\Controllers\ClubController@index')->name('clubs');
    Route::get('enroll', 'App\Http\Controllers\ClubController@clubEnroll')->name('clubs.enroll');
    Route::get('enroll/add/{club_id}', 'App\Http\Controllers\ClubController@clubEnrollAdd');
    Route::post('enroll/add/{club_id}', 'App\Http\Controllers\ClubController@clubEnrollInsert')->name('clubs.addenroll');
    Route::get('enroll/edit/{enroll_id}', 'App\Http\Controllers\ClubController@clubEnrollEdit');
    Route::post('enroll/edit/{enroll_id}', 'App\Http\Controllers\ClubController@clubEnrollUpdate')->name('clubs.editenroll');
    Route::post('enroll/remove/{enroll_id}', 'App\Http\Controllers\ClubController@clubEnrollRemove')->name('clubs.delenroll');
    Route::get('kind', 'App\Http\Controllers\ClubController@kindList')->name('clubs.kinds');
    Route::get('kind/add', 'App\Http\Controllers\ClubController@kindAdd');
    Route::post('kind/add', 'App\Http\Controllers\ClubController@kindInsert')->name('clubs.addkind');
    Route::get('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindEdit');
    Route::post('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindUpdate')->name('clubs.editkind');
    Route::post('kind/{kid}/remove', 'App\Http\Controllers\ClubController@kindRemove')->name('clubs.removekind');
    Route::get('kind/{kid}/up', 'App\Http\Controllers\ClubController@kindUp')->name('clubs.upkind');
    Route::get('kind/{kid}/down', 'App\Http\Controllers\ClubController@kindDown')->name('clubs.downkind');
    Route::get('add/{kid?}', 'App\Http\Controllers\ClubController@clubAdd');
    Route::post('add/{kid?}', 'App\Http\Controllers\ClubController@clubInsert')->name('clubs.add');
    Route::get('edit/{club_id}', 'App\Http\Controllers\ClubController@clubEdit');
    Route::post('edit/{club_id}', 'App\Http\Controllers\ClubController@clubUpdate')->name('clubs.edit');
    Route::post('remove/{club_id}', 'App\Http\Controllers\ClubController@clubRemove')->name('clubs.remove');
    Route::get('mail/{club_id}', 'App\Http\Controllers\ClubController@clubMail');
    Route::post('mail/{club_id}', 'App\Http\Controllers\ClubController@clubNotify')->name('clubs.mail');
    Route::post('prune/{club_id}', 'App\Http\Controllers\ClubController@clubPrune')->name('clubs.prune');
    Route::get('import/{kid?}', 'App\Http\Controllers\ClubController@clubUpload');
    Route::post('import/{kid?}', 'App\Http\Controllers\ClubController@clubImport')->name('clubs.import');
    Route::get('export/{kid}', 'App\Http\Controllers\ClubController@clubExport')->name('clubs.export');
    Route::get('repeat/{kid}', 'App\Http\Controllers\ClubController@clubRepetition')->name('clubs.repeat');
    Route::get('cash', 'App\Http\Controllers\ClubController@clubExportCash')->name('clubs.cash');
    Route::get('classroom/{kid}/{class_id?}', 'App\Http\Controllers\ClubController@clubClassroom')->name('clubs.classroom');
    Route::get('classroom/{kid}/export/{class_id}', 'App\Http\Controllers\ClubController@clubExportClass')->name('clubs.exportclass');
    Route::get('list/{kid?}', 'App\Http\Controllers\ClubController@clubList')->name('clubs.admin');
    Route::get('list/{kid}/enroll/{club_id}', 'App\Http\Controllers\ClubController@enrollList')->name('clubs.enrolls');
});

// Administrator Interface Routes...
Route::group(['prefix' => 'admin', 'middleware' => [ 'auth', 'admin' ] ], function () {
    Route::get('/', 'App\Http\Controllers\Admin\AdminController@index')->name('admin');
    Route::get('database/sync', 'App\Http\Controllers\Admin\SyncController@syncFromTpedu');
    Route::post('database/sync', 'App\Http\Controllers\Admin\SyncController@startSyncFromTpedu')->name('sync');
    Route::get('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@syncToAD');
    Route::post('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@startSyncToAD')->name('syncAD');
    Route::get('database/sync/google', 'App\Http\Controllers\Admin\SyncController@syncToGsuite');
    Route::post('database/sync/google', 'App\Http\Controllers\Admin\SyncController@startSyncToGsuite')->name('syncGsuite');
    Route::get('database/units', 'App\Http\Controllers\Admin\SchoolDataController@unitList');
    Route::post('database/units', 'App\Http\Controllers\Admin\SchoolDataController@unitUpdate')->name('units');
    Route::get('database/units/add', 'App\Http\Controllers\Admin\SchoolDataController@unitAdd');
    Route::post('database/units/add', 'App\Http\Controllers\Admin\SchoolDataController@unitInsert')->name('units.add');
    Route::get('database/units/role/add', 'App\Http\Controllers\Admin\SchoolDataController@roleAdd');
    Route::post('database/units/role/add', 'App\Http\Controllers\Admin\SchoolDataController@roleInsert')->name('roles.add');
    Route::get('database/classes', 'App\Http\Controllers\Admin\SchoolDataController@classList');
    Route::post('database/classes', 'App\Http\Controllers\Admin\SchoolDataController@classUpdate')->name('classes');
    Route::get('database/subjects', 'App\Http\Controllers\Admin\SchoolDataController@subjectList');
    Route::post('database/subjects', 'App\Http\Controllers\Admin\SchoolDataController@subjectUpdate')->name('subjects');
    Route::get('database/teachers/{unit?}', 'App\Http\Controllers\Admin\SchoolDataController@teacherList')->name('teachers');
    Route::get('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@teacherEdit');
    Route::post('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@teacherUpdate')->name('teachers.edit');
    Route::post('database/teachers/{uuid}/remove', 'App\Http\Controllers\Admin\SchoolDataController@teacherRemove')->name('teachers.remove');
    Route::get('database/students/{myclass?}', 'App\Http\Controllers\Admin\SchoolDataController@studentList')->name('students');
    Route::get('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentEdit');
    Route::post('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentUpdate')->name('students.edit');
    Route::post('database/students/{uuid}/remove', 'App\Http\Controllers\Admin\SchoolDataController@studentRemove')->name('students.remove');

    Route::get('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@index');
    Route::post('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@update')->name('menus');
    Route::get('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@add');
    Route::post('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@insert')->name('menus.add');
    Route::post('website/menus/remove/{menu}', 'App\Http\Controllers\Admin\MenuController@remove')->name('menus.remove');
    Route::get('website/permission', 'App\Http\Controllers\Admin\PermitController@index')->name('permission');
    Route::get('website/permission/add', 'App\Http\Controllers\Admin\PermitController@add');
    Route::post('website/permission/add', 'App\Http\Controllers\Admin\PermitController@insert')->name('permission.add');
    Route::get('website/permission/{id}/edit', 'App\Http\Controllers\Admin\PermitController@edit');
    Route::post('website/permission/{id}/edit', 'App\Http\Controllers\Admin\PermitController@update')->name('permission.edit');
    Route::post('website/permission/{id}/remove', 'App\Http\Controllers\Admin\PermitController@remove')->name('permission.remove');
    Route::get('website/permission/{id}/grant', 'App\Http\Controllers\Admin\PermitController@grantList');
    Route::post('website/permission/{id}/grant', 'App\Http\Controllers\Admin\PermitController@grantUpdate')->name('permission.grant');
});

