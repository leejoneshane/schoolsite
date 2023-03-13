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

// 登入
Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->middleware('firsttime')->name('login');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
Route::get('login/tpedu', 'App\Http\Controllers\Auth\TpeduController@redirect');
Route::get('login/tpedu/callback', 'App\Http\Controllers\Auth\TpeduController@handleCallback');
Route::get('login/{provider}', 'App\Http\Controllers\Auth\SocialiteController@redirect');
Route::get('login/{provider}/callback', 'App\Http\Controllers\Auth\SocialiteController@handleCallback');
Route::get('socialite', 'App\Http\Controllers\Auth\SocialiteController@socialite')->middleware('auth')->name('social');
Route::post('socialite/remove', 'App\Http\Controllers\Auth\SocialiteController@removeSocialite')->middleware('auth')->name('social.remove');

// 登出
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

// 註冊管理員
Route::get('register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->middleware('nouser')->name('register');
Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register')->middleware('nouser');

// 重設密碼
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

// 密碼驗證
Route::get('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@confirm');

// 郵件地址驗證
Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

// 電子報訂閱
Route::group(['prefix' => 'subscriber'], function () {
    Route::get('/list/{email?}', 'App\Http\Controllers\SubscriberController@index')->name('subscriber');
    Route::post('add/{news?}', 'App\Http\Controllers\SubscriberController@subscription')->name('subscriber.subscription');
    Route::get('delete/{news?}', 'App\Http\Controllers\SubscriberController@remove')->name('subscriber.cancel');
    Route::get('verify/{id}/{hash}', 'App\Http\Controllers\SubscriberController@verify')->name('subscriber.verify');
});

// 行事曆
Route::group(['prefix' => 'calendar'], function () {
    Route::get('/', 'App\Http\Controllers\CalendarController@calendar')->name('calendar');
    Route::get('event/add', 'App\Http\Controllers\CalendarController@eventAdd')->middleware('auth');
    Route::post('event/add', 'App\Http\Controllers\CalendarController@eventInsert')->middleware('auth')->name('calendar.addEvent');
    Route::get('event/edit/{event}', 'App\Http\Controllers\CalendarController@eventEdit')->middleware('auth');
    Route::post('event/edit/{event}', 'App\Http\Controllers\CalendarController@eventUpdate')->middleware('auth')->name('calendar.editEvent');
    Route::post('event/remove/{event}', 'App\Http\Controllers\CalendarController@eventRemove')->middleware('auth')->name('calendar.removeEvent');
    Route::get('seme', 'App\Http\Controllers\CalendarController@seme')->name('calendar.seme');
    Route::get('training', 'App\Http\Controllers\CalendarController@training')->name('calendar.training');
    Route::get('student', 'App\Http\Controllers\CalendarController@student')->name('calendar.student');
    Route::get('download', 'App\Http\Controllers\CalendarController@download')->name('calendar.download');
    Route::get('import', 'App\Http\Controllers\CalendarController@student')->name('calendar.import');
});

// 即時推播
Route::get('messager/list/online', 'App\Http\Controllers\MessagerController@list')->middleware('auth')->name('messager.list');
Route::post('messager/send', 'App\Http\Controllers\MessagerController@send')->middleware('auth')->name('messager.send');
Route::post('messager/broadcast', 'App\Http\Controllers\MessagerController@broadcast')->middleware('auth')->name('messager.broadcast');

// 學生課外社團
Route::group(['prefix' => 'club', 'middleware' => [ 'auth' ] ], function () {
    Route::get('/', 'App\Http\Controllers\ClubController@index')->name('clubs');
    Route::get('enroll', 'App\Http\Controllers\ClubController@clubEnroll')->name('clubs.enroll');
    Route::get('list/enroll/{club_id}/{section?}', 'App\Http\Controllers\ClubController@enrollList')->name('clubs.enrolls');
    Route::get('enroll/add/{club_id}', 'App\Http\Controllers\ClubController@enrollAdd');
    Route::post('enroll/add/{club_id}', 'App\Http\Controllers\ClubController@enrollInsert')->name('clubs.addenroll');
    Route::get('enroll/edit/{enroll_id}', 'App\Http\Controllers\ClubController@enrollEdit');
    Route::post('enroll/edit/{enroll_id}', 'App\Http\Controllers\ClubController@enrollUpdate')->name('clubs.editenroll');
    Route::post('enroll/remove/{enroll_id}', 'App\Http\Controllers\ClubController@enrollRemove')->name('clubs.delenroll');
    Route::get('enroll/append/{club_id}/{class?}', 'App\Http\Controllers\ClubController@enrollAppend');
    Route::post('enroll/append/{club_id}/{class?}', 'App\Http\Controllers\ClubController@enrollInsertAppend')->name('clubs.appendenroll');
    Route::get('enroll/fast/{club_id}', 'App\Http\Controllers\ClubController@enrollFastAppend');
    Route::post('enroll/fast/{club_id}', 'App\Http\Controllers\ClubController@enrollInsertFast')->name('clubs.fastappend');
    Route::get('enroll/import/{club_id}', 'App\Http\Controllers\ClubController@enrollImport');
    Route::post('enroll/import/{club_id}', 'App\Http\Controllers\ClubController@enrollImportOld')->name('clubs.importold');
    Route::get('enroll/notify/{club_id}', 'App\Http\Controllers\ClubController@enrollNotify')->name('clubs.notify');
    Route::get('enroll/export/{club_id}', 'App\Http\Controllers\ClubController@enrollExport')->name('clubs.exportenrolled');
    Route::post('enroll/valid/{enroll_id}', 'App\Http\Controllers\ClubController@enrollValid')->name('clubs.valid');
    Route::post('enroll/deny/{enroll_id}', 'App\Http\Controllers\ClubController@enrollDeny')->name('clubs.deny');
    Route::get('time/{club_id}', 'App\Http\Controllers\ClubController@enrollExportTime')->name('clubs.exporttimeseq');
    Route::get('roll/{club_id}', 'App\Http\Controllers\ClubController@enrollExportRoll')->name('clubs.exportroll');
    Route::get('kind', 'App\Http\Controllers\ClubController@kindList')->name('clubs.kinds');
    Route::get('kind/add', 'App\Http\Controllers\ClubController@kindAdd');
    Route::post('kind/add', 'App\Http\Controllers\ClubController@kindInsert')->name('clubs.addkind');
    Route::get('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindEdit');
    Route::post('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindUpdate')->name('clubs.editkind');
    Route::post('kind/{kid}/remove', 'App\Http\Controllers\ClubController@kindRemove')->name('clubs.removekind');
    Route::get('kind/{kid}/up', 'App\Http\Controllers\ClubController@kindUp')->name('clubs.upkind');
    Route::get('kind/{kid}/down', 'App\Http\Controllers\ClubController@kindDown')->name('clubs.downkind');
    Route::get('list/{kid?}', 'App\Http\Controllers\ClubController@clubList')->name('clubs.admin');
    Route::get('add/{kid?}', 'App\Http\Controllers\ClubController@clubAdd');
    Route::post('add/{kid?}', 'App\Http\Controllers\ClubController@clubInsert')->name('clubs.add');
    Route::get('edit/{club_id}', 'App\Http\Controllers\ClubController@clubEdit');
    Route::post('edit/{club_id}', 'App\Http\Controllers\ClubController@clubUpdate')->name('clubs.edit');
    Route::post('remove/{club_id}', 'App\Http\Controllers\ClubController@clubRemove')->name('clubs.remove');
    Route::get('mail/{club_id}', 'App\Http\Controllers\ClubController@clubMail');
    Route::post('mail/{club_id}', 'App\Http\Controllers\ClubController@clubNotify')->name('clubs.mail');
    Route::post('prune/{club_id}', 'App\Http\Controllers\ClubController@clubPrune')->name('clubs.prune');
    Route::get('section/{club_id}', 'App\Http\Controllers\ClubController@sectionList')->name('clubs.sections');
    Route::get('section/add/{club_id}', 'App\Http\Controllers\ClubController@sectionAdd');
    Route::post('section/add/{club_id}', 'App\Http\Controllers\ClubController@sectionInsert')->name('clubs.addsection');
    Route::get('section/edit/{section_id}', 'App\Http\Controllers\ClubController@sectionEdit');
    Route::post('section/edit/{section_id}', 'App\Http\Controllers\ClubController@sectionUpdate')->name('clubs.editsection');
    Route::post('section/remove/{section_id}', 'App\Http\Controllers\ClubController@sectionRemove')->name('clubs.removesection');
    Route::get('import/{kid?}', 'App\Http\Controllers\ClubController@clubUpload');
    Route::post('import/{kid?}', 'App\Http\Controllers\ClubController@clubImport')->name('clubs.import');
    Route::get('export/{kid}', 'App\Http\Controllers\ClubController@clubExport')->name('clubs.export');
    Route::get('repeat/{kid}', 'App\Http\Controllers\ClubController@clubRepetition')->name('clubs.repeat');
    Route::get('cash', 'App\Http\Controllers\ClubController@clubExportCash')->name('clubs.cash');
    Route::get('classroom/{kid}/{class_id?}', 'App\Http\Controllers\ClubController@clubClassroom')->name('clubs.classroom');
    Route::get('classroom/{kid}/export/{class_id}', 'App\Http\Controllers\ClubController@clubExportClass')->name('clubs.exportclass');

});

//明日小作家
Route::group(['prefix' => 'writing', 'middleware' => [ 'auth'] ], function () {
    Route::get('list', 'App\Http\Controllers\WritingController@index')->name('writing');
    Route::get('genres', 'App\Http\Controllers\WritingController@genres')->name('writing.genres');
    Route::get('genre/add', 'App\Http\Controllers\WritingController@addGenre');
    Route::post('genre/add', 'App\Http\Controllers\WritingController@insertGenre')->name('writing.addgenre');
    Route::get('genre/edit/{genre}', 'App\Http\Controllers\WritingController@editGenre');
    Route::post('genre/edit/{genre}', 'App\Http\Controllers\WritingController@updateGenre')->name('writing.editgenre');
    Route::post('genre/remove/{genre}', 'App\Http\Controllers\WritingController@removeGenre')->name('writing.removegenre');
    Route::get('add/{genre}', 'App\Http\Controllers\WritingController@add');
    Route::post('add/{genre}', 'App\Http\Controllers\WritingController@insert')->name('writing.add');
    Route::get('edit/{id}', 'App\Http\Controllers\WritingController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\WritingController@update')->name('writing.edit');
    Route::post('remove/{id}', 'App\Http\Controllers\WritingController@remove')->name('writing.remove');
    Route::get('view/{id}', 'App\Http\Controllers\WritingController@show')->name('writing.view');
});

// 網路朝會
Route::group(['prefix' => 'meeting', 'middleware' => [ 'auth'] ], function () {
    Route::get('list/{date?}', 'App\Http\Controllers\MeetingController@index')->name('meeting');
    Route::get('add', 'App\Http\Controllers\MeetingController@add');
    Route::post('add', 'App\Http\Controllers\MeetingController@insert')->name('meeting.add');
    Route::get('edit/{id}', 'App\Http\Controllers\MeetingController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\MeetingController@update')->name('meeting.edit');
    Route::post('remove/{id}', 'App\Http\Controllers\MeetingController@remove')->name('meeting.remove');
    Route::post('image-upload', 'App\Http\Controllers\MeetingController@storeImage')->name('meeting.imageupload');
});

// 年資統計
Route::group(['prefix' => 'seniority', 'middleware' => [ 'auth'] ], function () {
    Route::get('list/{year?}', 'App\Http\Controllers\SeniorityController@index')->name('seniority');
    Route::get('import', 'App\Http\Controllers\SeniorityController@upload');
    Route::post('import', 'App\Http\Controllers\SeniorityController@import')->name('seniority.import');
    Route::get('export/{year?}', 'App\Http\Controllers\SeniorityController@export')->name('seniority.export');
    Route::post('confirm', 'App\Http\Controllers\SeniorityController@confirm')->name('seniority.confirm');
    Route::post('cancel', 'App\Http\Controllers\SeniorityController@cancel')->name('seniority.cancel');
    Route::post('update', 'App\Http\Controllers\SeniorityController@update')->name('seniority.update');
});

// 職務編排
Route::group(['prefix' => 'organize', 'middleware' => [ 'auth'] ], function () {
    Route::get('index/{year?}', 'App\Http\Controllers\OrganizeController@index')->name('organize');
    Route::post('survey/{uuid}', 'App\Http\Controllers\OrganizeController@survey')->name('organize.survey');
    Route::get('vacancy', 'App\Http\Controllers\OrganizeController@vacancy')->name('organize.vacancy');
    Route::get('vacancy/reset', 'App\Http\Controllers\OrganizeController@reset')->name('organize.reset');
    Route::post('vacancy/stage', 'App\Http\Controllers\OrganizeController@stage')->name('organize.stage');
    Route::post('vacancy/special', 'App\Http\Controllers\OrganizeController@special')->name('organize.special');
    Route::post('vacancy/shortfall', 'App\Http\Controllers\OrganizeController@shortfall')->name('organize.shortfall');
    Route::post('vacancy/release', 'App\Http\Controllers\OrganizeController@release')->name('organize.release');
    Route::post('vacancy/reserve', 'App\Http\Controllers\OrganizeController@reserve')->name('organize.reserve');
    Route::post('vacancy/release/all', 'App\Http\Controllers\OrganizeController@releaseAll')->name('organize.releaseall');
    Route::get('setting', 'App\Http\Controllers\OrganizeController@setting');
    Route::post('setting', 'App\Http\Controllers\OrganizeController@saveSettings')->name('organize.setting');
    Route::get('arrange/{search?}', 'App\Http\Controllers\OrganizeController@index')->name('organize.arrange');
    Route::post('arrange/assign', 'App\Http\Controllers\OrganizeController@assign')->name('organize.assign');
    Route::post('arrange/unassign', 'App\Http\Controllers\OrganizeController@unassign')->name('organize.unassign');
    Route::get('list/vacancy/{year?}', 'App\Http\Controllers\OrganizeController@listVacancy')->name('organize.listvacancy');
    Route::post('list/survey', 'App\Http\Controllers\OrganizeController@listSurvey')->name('organize.listsurvey');
    Route::get('list/result/{year?}', 'App\Http\Controllers\OrganizeController@listResult')->name('organize.listresult');
});

// 場地/設備預約
Route::group(['prefix' => 'venue', 'middleware' => [ 'auth'] ], function () {
    Route::get('/', 'App\Http\Controllers\VenueController@index')->name('venues');
    Route::get('add', 'App\Http\Controllers\VenueController@add');
    Route::post('add', 'App\Http\Controllers\VenueController@insert')->name('venue.add');
    Route::get('edit/{id}', 'App\Http\Controllers\VenueController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\VenueController@update')->name('venue.edit');
    Route::post('remove/{id}', 'App\Http\Controllers\VenueController@remove')->name('venue.remove');
    Route::get('reserve/list/{id}/date/{date?}', 'App\Http\Controllers\VenueController@reserve')->name('venue.reserve');
    Route::post('reserve/view', 'App\Http\Controllers\VenueController@show')->name('venue.reserve.view');
    Route::post('reserve/add', 'App\Http\Controllers\VenueController@reserveAdd')->name('venue.reserve.add');
    Route::post('reserve/insert', 'App\Http\Controllers\VenueController@reserveInsert')->name('venue.reserve.insert');
    Route::post('reserve/edit', 'App\Http\Controllers\VenueController@reserveEdit')->name('venue.reserve.edit');
    Route::post('reserve/update', 'App\Http\Controllers\VenueController@reserveUpdate')->name('venue.reserve.update');
});

// 修繕登記
Route::group(['prefix' => 'repair', 'middleware' => [ 'auth'] ], function () {
    Route::get('/', 'App\Http\Controllers\RepairController@index')->name('repair');
    Route::get('list/{kind?}', 'App\Http\Controllers\RepairController@list')->name('repair.list');
    Route::get('kinds/add', 'App\Http\Controllers\RepairController@addKind');
    Route::post('kinds/add', 'App\Http\Controllers\RepairController@insertKind')->name('repair.addkind');
    Route::get('kinds/edit/{kind}', 'App\Http\Controllers\RepairController@editKind');
    Route::post('kinds/edit/{kind}', 'App\Http\Controllers\RepairController@updateKind')->name('repair.editkind');
    Route::post('kinds/remove/{kind}', 'App\Http\Controllers\RepairController@removeKind')->name('repair.removekind');
    Route::get('report/{kind}', 'App\Http\Controllers\RepairController@report');
    Route::post('report/{kind}', 'App\Http\Controllers\RepairController@insertJob')->name('repair.report');
    Route::post('report/remove/{job}', 'App\Http\Controllers\RepairController@removeJob')->name('repair.removejob');
    Route::get('reply/{job}', 'App\Http\Controllers\RepairController@reply');
    Route::post('reply/{job}', 'App\Http\Controllers\RepairController@insertReply')->name('repair.reply');
    Route::post('reply/remove/{reply}', 'App\Http\Controllers\RepairController@removeReply')->name('repair.removereply');
    Route::post('image-upload', 'App\Http\Controllers\RepairController@storeImage')->name('repair.imageupload');
});

// 學生名單填報
Route::group(['prefix' => 'roster', 'middleware' => [ 'auth'] ], function () {
    Route::get('list/{section?}', 'App\Http\Controllers\RosterController@list')->name('rosters');
    Route::get('summary/{id}/{section?}', 'App\Http\Controllers\RosterController@summary')->name('roster.summary');
    Route::get('add', 'App\Http\Controllers\RosterController@add');
    Route::post('add', 'App\Http\Controllers\RosterController@insert')->name('roster.add');
    Route::get('edit/{id}', 'App\Http\Controllers\RosterController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\RosterController@update')->name('roster.edit');
    Route::post('remove/{id}', 'App\Http\Controllers\RosterController@remove')->name('roster.remove');
    Route::post('reset/{id}', 'App\Http\Controllers\RosterController@reset')->name('roster.reset');
    Route::get('enroll/{id}/{class?}', 'App\Http\Controllers\RosterController@enroll');
    Route::post('enroll/{id}/{class?}', 'App\Http\Controllers\RosterController@save_enroll')->name('roster.enroll');
    Route::get('view/{id}/{section}/{class?}', 'App\Http\Controllers\RosterController@show')->name('roster.show');
    Route::get('download/{id}/{section}', 'App\Http\Controllers\RosterController@download')->name('roster.download');
});

// 分組座位表
Route::group(['prefix' => 'seats', 'middleware' => [ 'auth'] ], function () {
    Route::get('/', 'App\Http\Controllers\SeatsController@index')->name('seats');
    Route::get('theme', 'App\Http\Controllers\SeatsController@theme')->name('seats.theme');
    Route::get('theme/add', 'App\Http\Controllers\SeatsController@addTheme');
    Route::post('theme/add', 'App\Http\Controllers\SeatsController@insertTheme')->name('seats.addtheme');
    Route::get('theme/view', 'App\Http\Controllers\SeatsController@showTheme')->name('seats.viewtheme');
    Route::get('theme/edit/{id}', 'App\Http\Controllers\SeatsController@editTheme');
    Route::post('theme/edit/{id}', 'App\Http\Controllers\SeatsController@updateTheme')->name('seats.edittheme');
    Route::post('theme/remove/{id}', 'App\Http\Controllers\SeatsController@removeTheme')->name('seats.removetheme');
    Route::get('add', 'App\Http\Controllers\SeatsController@add');
    Route::post('add', 'App\Http\Controllers\SeatsController@insert')->name('seats.add');
    Route::get('view/{id}', 'App\Http\Controllers\SeatsController@show')->name('seats.view');
    Route::get('view/{id}/group', 'App\Http\Controllers\SeatsController@group')->name('seats.group');
    Route::post('auto/{id}', 'App\Http\Controllers\SeatsController@auto')->name('seats.auto');
    Route::get('edit/{id}', 'App\Http\Controllers\SeatsController@edit')->name('seats.edit');
    Route::get('change/{id}', 'App\Http\Controllers\SeatsController@change');
    Route::post('change/{id}', 'App\Http\Controllers\SeatsController@updateChange')->name('seats.change');
    Route::post('remove/{id}', 'App\Http\Controllers\SeatsController@remove')->name('seats.remove');
    Route::post('assign', 'App\Http\Controllers\SeatsController@assign')->name('seats.assign');
    Route::post('unassign', 'App\Http\Controllers\SeatsController@unassign')->name('seats.unassign');
});

// 管理後台
Route::group(['prefix' => 'admin', 'middleware' => [ 'auth', 'admin' ] ], function () {
    Route::get('/', 'App\Http\Controllers\Admin\AdminController@index')->name('admin');
// 快取資料庫同步作業
    Route::get('database/sync', 'App\Http\Controllers\Admin\SyncController@syncFromTpedu');
    Route::post('database/sync', 'App\Http\Controllers\Admin\SyncController@startSyncFromTpedu')->name('sync');
    Route::get('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@syncToAD');
    Route::post('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@startSyncToAD')->name('syncAD');
    Route::get('database/sync/google', 'App\Http\Controllers\Admin\SyncController@syncToGsuite');
    Route::post('database/sync/google', 'App\Http\Controllers\Admin\SyncController@startSyncToGsuite')->name('syncGsuite');
// 單一身份驗證資料管理
    Route::get('database/units', 'App\Http\Controllers\Admin\SchoolDataController@unitList');
    Route::post('database/units', 'App\Http\Controllers\Admin\SchoolDataController@unitUpdate')->name('units');
    Route::get('database/units/add', 'App\Http\Controllers\Admin\SchoolDataController@unitAdd');
    Route::post('database/units/add', 'App\Http\Controllers\Admin\SchoolDataController@unitInsert')->name('units.add');
    Route::get('database/units/role/add', 'App\Http\Controllers\Admin\SchoolDataController@roleAdd');
    Route::post('database/units/role/add', 'App\Http\Controllers\Admin\SchoolDataController@roleInsert')->name('roles.add');
    Route::get('database/classes', 'App\Http\Controllers\Admin\SchoolDataController@classList');
    Route::post('database/classes', 'App\Http\Controllers\Admin\SchoolDataController@classUpdate')->name('classes');
    Route::get('database/domains', 'App\Http\Controllers\Admin\SchoolDataController@domainList');
    Route::post('database/domains', 'App\Http\Controllers\Admin\SchoolDataController@domainUpdate')->name('domains');
    Route::get('database/domains/add', 'App\Http\Controllers\Admin\SchoolDataController@domainAdd');
    Route::post('database/domains/add', 'App\Http\Controllers\Admin\SchoolDataController@domainInsert')->name('domains.add');
    Route::get('database/subjects', 'App\Http\Controllers\Admin\SchoolDataController@subjectList');
    Route::post('database/subjects', 'App\Http\Controllers\Admin\SchoolDataController@subjectUpdate')->name('subjects');
    Route::get('database/teachers/{search?}', 'App\Http\Controllers\Admin\SchoolDataController@teacherList')->name('teachers');
    Route::get('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@teacherEdit');
    Route::post('database/teachers/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@teacherUpdate')->name('teachers.edit');
    Route::post('database/teachers/{uuid}/sync', 'App\Http\Controllers\Admin\SchoolDataController@teacherSync')->name('teachers.sync');
    Route::post('database/teachers/{uuid}/remove', 'App\Http\Controllers\Admin\SchoolDataController@teacherRemove')->name('teachers.remove');
    Route::get('database/students/{search?}', 'App\Http\Controllers\Admin\SchoolDataController@studentList')->name('students');
    Route::get('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentEdit');
    Route::post('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentUpdate')->name('students.edit');
    Route::post('database/students/{uuid}/sync', 'App\Http\Controllers\Admin\SchoolDataController@studentSync')->name('students.sync');
    Route::post('database/students/{uuid}/remove', 'App\Http\Controllers\Admin\SchoolDataController@studentRemove')->name('students.remove');
// 選單管理
    Route::get('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@index');
    Route::post('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@update')->name('menus');
    Route::get('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@add');
    Route::post('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@insert')->name('menus.add');
    Route::post('website/menus/{menu}/remove', 'App\Http\Controllers\Admin\MenuController@remove')->name('menus.remove');
// 權限管理
    Route::get('website/permission', 'App\Http\Controllers\Admin\PermitController@index')->name('permission');
    Route::get('website/permission/add', 'App\Http\Controllers\Admin\PermitController@add');
    Route::post('website/permission/add', 'App\Http\Controllers\Admin\PermitController@insert')->name('permission.add');
    Route::get('website/permission/{id}/edit', 'App\Http\Controllers\Admin\PermitController@edit');
    Route::post('website/permission/{id}/edit', 'App\Http\Controllers\Admin\PermitController@update')->name('permission.edit');
    Route::post('website/permission/{id}/remove', 'App\Http\Controllers\Admin\PermitController@remove')->name('permission.remove');
    Route::get('website/permission/{id}/grant', 'App\Http\Controllers\Admin\PermitController@grantList');
    Route::post('website/permission/{id}/grant', 'App\Http\Controllers\Admin\PermitController@grantUpdate')->name('permission.grant');
// 電子報管理
    Route::get('website/news', 'App\Http\Controllers\Admin\NewsController@index')->name('news');
    Route::get('website/news/add', 'App\Http\Controllers\Admin\NewsController@add');
    Route::post('website/news/add', 'App\Http\Controllers\Admin\NewsController@insert')->name('news.add');
    Route::get('website/news/{news}/edit', 'App\Http\Controllers\Admin\NewsController@edit');
    Route::post('website/news/{news}/edit', 'App\Http\Controllers\Admin\NewsController@update')->name('news.edit');
    Route::post('website/news/{news}/remove', 'App\Http\Controllers\Admin\NewsController@remove')->name('news.remove');
    Route::get('website/news/{news}/subscribers', 'App\Http\Controllers\Admin\NewsController@subscribers')->name('subscribers');
    Route::post('website/news/{news}/subscribers/add', 'App\Http\Controllers\Admin\NewsController@insertSub')->name('subscriber.add');
    Route::post('website/news/{news}/subscribers/{id}/edit', 'App\Http\Controllers\Admin\NewsController@updateSub')->name('subscriber.edit');
    Route::post('website/news/{news}/subscribers/{id}/remove', 'App\Http\Controllers\Admin\NewsController@removeSub')->name('subscriber.remove');
//瀏覽歷程
    Route::get('website/watchdog', 'App\Http\Controllers\Admin\WatchdogController@index')->name('watchdog');
});