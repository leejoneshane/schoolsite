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
    Route::get('list/{email?}', 'App\Http\Controllers\SubscriberController@index')->name('subscriber');
    Route::post('add/{news?}', 'App\Http\Controllers\SubscriberController@subscription')->name('subscriber.subscription');
    Route::post('delete/{news?}', 'App\Http\Controllers\SubscriberController@remove')->name('subscriber.cancel');
    Route::get('verify/{id}/{hash}', 'App\Http\Controllers\SubscriberController@verify')->name('subscriber.verify');
    Route::get('resent/{email?}', 'App\Http\Controllers\SubscriberController@resent')->name('subscriber.resent');
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
Route::group(['prefix' => 'messager', 'middleware' => [ 'auth' ]], function () {
    Route::get('list/online', 'App\Http\Controllers\MessagerController@list')->middleware('auth')->name('messager.list');
    Route::post('send', 'App\Http\Controllers\MessagerController@send')->middleware('auth')->name('messager.send');
    Route::post('broadcast', 'App\Http\Controllers\MessagerController@broadcast')->middleware('auth')->name('messager.broadcast');
});

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
    Route::get('enroll/append/{club_id}/{section}/{class?}', 'App\Http\Controllers\ClubController@enrollAppend');
    Route::post('enroll/append/{club_id}/{section}/{class?}', 'App\Http\Controllers\ClubController@enrollInsertAppend')->name('clubs.appendenroll');
    Route::get('enroll/fast/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollFastAppend');
    Route::post('enroll/fast/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollInsertFast')->name('clubs.fastappend');
    Route::get('enroll/import/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollImport');
    Route::post('enroll/import/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollImportOld')->name('clubs.importold');
    Route::get('enroll/notify/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollNotify')->name('clubs.notify');
    Route::get('enroll/export/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollExport')->name('clubs.exportenrolled');
    Route::post('enroll/valid/{enroll_id}', 'App\Http\Controllers\ClubController@enrollValid')->name('clubs.valid');
    Route::post('enroll/deny/{enroll_id}', 'App\Http\Controllers\ClubController@enrollDeny')->name('clubs.deny');
    Route::get('enroll/group/{enroll_id}', 'App\Http\Controllers\ClubController@enrollGroupSelect');
    Route::post('enroll/group/{enroll_id}', 'App\Http\Controllers\ClubController@enrollGroupUpdate')->name('clubs.selgrp');
    Route::get('enroll/autodevide/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollDevide');
    Route::post('enroll/autodevide/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollConquer')->name('clubs.devide');
    Route::get('time/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollExportTime')->name('clubs.exporttimeseq');
    Route::get('roll/{club_id}/{section}', 'App\Http\Controllers\ClubController@enrollExportRoll')->name('clubs.exportroll');
    Route::get('kind', 'App\Http\Controllers\ClubController@kindList')->name('clubs.kinds');
    Route::get('kind/add', 'App\Http\Controllers\ClubController@kindAdd');
    Route::post('kind/add', 'App\Http\Controllers\ClubController@kindInsert')->name('clubs.addkind');
    Route::get('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindEdit');
    Route::post('kind/{kid}/edit', 'App\Http\Controllers\ClubController@kindUpdate')->name('clubs.editkind');
    Route::post('kind/{kid}/remove', 'App\Http\Controllers\ClubController@kindRemove')->name('clubs.removekind');
    Route::get('kind/{kid}/up', 'App\Http\Controllers\ClubController@kindUp')->name('clubs.upkind');
    Route::get('kind/{kid}/down', 'App\Http\Controllers\ClubController@kindDown')->name('clubs.downkind');
    Route::get('list/{kid?}', 'App\Http\Controllers\ClubController@clubList')->name('clubs.admin');
    Route::get('manage', 'App\Http\Controllers\ClubController@clubManage')->name('clubs.manage');
    Route::get('add/{kid?}', 'App\Http\Controllers\ClubController@clubAdd');
    Route::post('add/{kid?}', 'App\Http\Controllers\ClubController@clubInsert')->name('clubs.add');
    Route::get('edit/{club_id}', 'App\Http\Controllers\ClubController@clubEdit');
    Route::post('edit/{club_id}', 'App\Http\Controllers\ClubController@clubUpdate')->name('clubs.edit');
    Route::post('remove/{club_id}', 'App\Http\Controllers\ClubController@clubRemove')->name('clubs.remove');
    Route::get('mail/{club_id}/{section?}', 'App\Http\Controllers\ClubController@clubMail');
    Route::post('mail/{club_id}/{section?}', 'App\Http\Controllers\ClubController@clubNotify')->name('clubs.mail');
    Route::post('prune/{club_id}/{section?}', 'App\Http\Controllers\ClubController@clubPrune')->name('clubs.prune');
    Route::get('section/{club_id}', 'App\Http\Controllers\ClubController@sectionList')->name('clubs.sections');
    Route::get('section/add/{club_id}/{section?}', 'App\Http\Controllers\ClubController@sectionAdd');
    Route::post('section/add/{club_id}/{section?}', 'App\Http\Controllers\ClubController@sectionInsert')->name('clubs.addsection');
    Route::get('section/edit/{section_id}', 'App\Http\Controllers\ClubController@sectionEdit');
    Route::post('section/edit/{section_id}', 'App\Http\Controllers\ClubController@sectionUpdate')->name('clubs.editsection');
    Route::post('section/remove/{section_id}', 'App\Http\Controllers\ClubController@sectionRemove')->name('clubs.removesection');
    Route::get('import/{kid?}', 'App\Http\Controllers\ClubController@clubUpload');
    Route::post('import/{kid?}', 'App\Http\Controllers\ClubController@clubImport')->name('clubs.import');
    Route::get('export/{kid}', 'App\Http\Controllers\ClubController@clubExport')->name('clubs.export');
    Route::get('repeat/{kid}/{section?}', 'App\Http\Controllers\ClubController@clubRepetition')->name('clubs.repeat');
    Route::get('cash/{section?}', 'App\Http\Controllers\ClubController@clubExportCash')->name('clubs.cash');
    Route::get('classroom/{kid}/{section?}/{class_id?}', 'App\Http\Controllers\ClubController@clubClassroom')->name('clubs.classroom');
    Route::get('classroom/{kid}/{section}/export/{class_id}', 'App\Http\Controllers\ClubController@clubExportClass')->name('clubs.exportclass');
    Route::get('tutor/{section?}', 'App\Http\Controllers\ClubController@clubTutor')->name('clubs.tutor');
});

//午餐調查
Route::group(['prefix' => 'lunch', 'middleware' => [ 'auth'] ], function () {
    Route::get('survey/{section?}', 'App\Http\Controllers\LunchController@index')->name('lunch');
    Route::get('config/{section}', 'App\Http\Controllers\LunchController@setting');
    Route::post('config/{section}', 'App\Http\Controllers\LunchController@save')->name('lunch.config');
    Route::post('save/survey', 'App\Http\Controllers\LunchController@survey')->name('lunch.survey');
    Route::get('download/{section?}', 'App\Http\Controllers\LunchController@downloadAll')->name('lunch.downloadAll');
    Route::get('class/download/{section}/{class_id}', 'App\Http\Controllers\LunchController@download')->name('lunch.download');
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

// 教師清單 JSON
Route::group(['prefix' => 'teachers', 'middleware' => [ 'auth'] ], function () {
    Route::get('all', 'App\Http\Controllers\DataController@all')->name('teachers.all');
    Route::get('domain/{domain_id}', 'App\Http\Controllers\DataController@domain')->name('teachers.bydomain');
    Route::get('class/{class_id}', 'App\Http\Controllers\DataController@class')->name('teachers.byclass');
    Route::get('grade/{grade_id}', 'App\Http\Controllers\DataController@grade')->name('teachers.bygrade');
});

// 公開課
Route::group(['prefix' => 'public', 'middleware' => [ 'auth'] ], function () {
    Route::get('list/{section?}', 'App\Http\Controllers\PublicController@index')->name('public');
    Route::post('add', 'App\Http\Controllers\PublicController@add')->name('public.reserve');
    Route::post('insert', 'App\Http\Controllers\PublicController@insert')->name('public.add');
    Route::get('edit/{id}', 'App\Http\Controllers\PublicController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\PublicController@update')->name('public.edit');
    Route::get('new/{section}', 'App\Http\Controllers\PublicController@new');
    Route::post('new/{section}', 'App\Http\Controllers\PublicController@append')->name('public.append');
    Route::post('remove/{id}', 'App\Http\Controllers\PublicController@remove')->name('public.remove');
    Route::post('view', 'App\Http\Controllers\PublicController@show')->name('public.view');
    Route::get('perm', 'App\Http\Controllers\PublicController@perm');
    Route::post('perm', 'App\Http\Controllers\PublicController@updatePerm')->name('public.permission');
    Route::get('export/{section}', 'App\Http\Controllers\PublicController@export')->name('public.export');
    Route::get('download/Word/{section}/{domain_id}', 'App\Http\Controllers\PublicController@docx')->name('public.downloadWord');
    Route::get('download/PDF/{section}/{domain_id}', 'App\Http\Controllers\PublicController@pdf')->name('public.downloadPDF');
    Route::get('download/Excel/{section}', 'App\Http\Controllers\PublicController@excel')->name('public.excel');
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
    Route::get('list/{search?}', 'App\Http\Controllers\SeniorityController@index')->name('seniority');
    Route::get('import', 'App\Http\Controllers\SeniorityController@upload');
    Route::post('import', 'App\Http\Controllers\SeniorityController@import')->name('seniority.import');
    Route::get('export/{year?}', 'App\Http\Controllers\SeniorityController@export')->name('seniority.export');
    Route::get('future', 'App\Http\Controllers\SeniorityController@future')->name('seniority.future');
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
    Route::get('arrange/{search?}', 'App\Http\Controllers\OrganizeController@arrange')->name('organize.arrange');
    Route::post('arrange/assign', 'App\Http\Controllers\OrganizeController@assign')->name('organize.assign');
    Route::post('arrange/unassign', 'App\Http\Controllers\OrganizeController@unassign')->name('organize.unassign');
    Route::get('list/vacancy/{year?}', 'App\Http\Controllers\OrganizeController@listVacancy')->name('organize.listvacancy');
    Route::post('list/survey/{tag?}', 'App\Http\Controllers\OrganizeController@listSurvey')->name('organize.listsurvey');
    Route::get('list/result/{year?}', 'App\Http\Controllers\OrganizeController@listResult')->name('organize.listresult');
});

// 場地/設備預約
Route::group(['prefix' => 'venue', 'middleware' => [ 'auth'] ], function () {
    Route::get('index', 'App\Http\Controllers\VenueController@index')->name('venues');
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
    Route::get('index', 'App\Http\Controllers\RepairController@index')->name('repair');
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

// 公假單套印
Route::group(['prefix' => 'dayoff', 'middleware' => [ 'auth'] ], function () {
    Route::get('/', 'App\Http\Controllers\DayoffController@index')->name('dayoff');
    Route::get('add', 'App\Http\Controllers\DayoffController@add');
    Route::post('add', 'App\Http\Controllers\DayoffController@insert')->name('dayoff.add');
    Route::get('edit/{id}', 'App\Http\Controllers\DayoffController@edit');
    Route::post('edit/{id}', 'App\Http\Controllers\DayoffController@update')->name('dayoff.edit');
    Route::post('remove/{id}', 'App\Http\Controllers\DayoffController@remove')->name('dayoff.remove');
    Route::get('sudents/{id?}', 'App\Http\Controllers\DayoffController@list')->name('dayoff.students');
    Route::get('sudents/{id}/class/{class?}', 'App\Http\Controllers\DayoffController@classAdd');
    Route::post('sudents/{id}/class/{class?}', 'App\Http\Controllers\DayoffController@classInsert')->name('dayoff.classadd');
    Route::get('sudents/{id}/fastadd', 'App\Http\Controllers\DayoffController@fastAdd');
    Route::post('sudents/{id}/fastadd', 'App\Http\Controllers\DayoffController@fastInsert')->name('dayoff.fastadd');
    Route::get('sudents/{id}/import/club', 'App\Http\Controllers\DayoffController@importClub');
    Route::post('sudents/{id}/import/club', 'App\Http\Controllers\DayoffController@importClubSave')->name('dayoff.importclub');
    Route::get('sudents/{id}/import/roster', 'App\Http\Controllers\DayoffController@importRoster');
    Route::post('sudents/{id}/import/roster', 'App\Http\Controllers\DayoffController@importRosterSave')->name('dayoff.importroster');
    Route::post('sudents/{id}/remove', 'App\Http\Controllers\DayoffController@removeStudent')->name('dayoff.delstudent');
    Route::get('sudents/{id}/remove/all', 'App\Http\Controllers\DayoffController@removeStudents')->name('dayoff.empty');
    Route::get('download/{id}', 'App\Http\Controllers\DayoffController@download')->name('dayoff.download');
    Route::get('print/{id}', 'App\Http\Controllers\DayoffController@print')->name('dayoff.print');
    Route::get('perm', 'App\Http\Controllers\DayoffController@perm');
    Route::post('perm', 'App\Http\Controllers\DayoffController@updatePerm')->name('dayoff.permission');
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
    Route::get('database/sync', 'App\Http\Controllers\Admin\SyncController@syncFromLDAP');
    Route::post('database/sync', 'App\Http\Controllers\Admin\SyncController@startSyncFromLDAP')->name('sync');
    Route::get('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@syncToMSAD');
    Route::post('database/sync/ad', 'App\Http\Controllers\Admin\SyncController@startSyncToMSAD')->name('syncAD');
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
    Route::post('database/teachers/{uuid}/restore', 'App\Http\Controllers\Admin\SchoolDataController@teacherRestore')->name('teachers.restore');
    Route::post('database/teachers/{uuid}/destroy', 'App\Http\Controllers\Admin\SchoolDataController@teacherDestroy')->name('teachers.destroy');
    Route::get('database/students/{search?}', 'App\Http\Controllers\Admin\SchoolDataController@studentList')->name('students');
    Route::get('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentEdit');
    Route::post('database/students/{uuid}/edit', 'App\Http\Controllers\Admin\SchoolDataController@studentUpdate')->name('students.edit');
    Route::post('database/students/{uuid}/pwd', 'App\Http\Controllers\Admin\SchoolDataController@studentPwd')->name('students.password');
    Route::post('database/students/{uuid}/sync', 'App\Http\Controllers\Admin\SchoolDataController@studentSync')->name('students.sync');
    Route::post('database/students/{uuid}/remove', 'App\Http\Controllers\Admin\SchoolDataController@studentRemove')->name('students.remove');
    Route::post('database/students/{uuid}/restore', 'App\Http\Controllers\Admin\SchoolDataController@studentRestore')->name('students.restore');
    Route::post('database/students/{uuid}/destroy', 'App\Http\Controllers\Admin\SchoolDataController@studentDestroy')->name('students.destroy');
// 選單管理
    Route::get('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@index');
    Route::post('website/menus/{menu?}', 'App\Http\Controllers\Admin\MenuController@update')->name('menus');
    Route::get('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@add');
    Route::post('website/menus/add/{menu?}', 'App\Http\Controllers\Admin\MenuController@insert')->name('menus.add');
    Route::post('website/menus/{menu}/remove', 'App\Http\Controllers\Admin\MenuController@remove')->name('menus.remove');
// 權限管理
    Route::get('website/permission', 'App\Http\Controllers\Admin\PermitController@index')->name('permission');
    Route::get('website/permission/admin', 'App\Http\Controllers\Admin\PermitController@admin');
    Route::post('website/permission/admin', 'App\Http\Controllers\Admin\PermitController@adminUpdate')->name('permission.admin');
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
    Route::post('website/watchdog/export', 'App\Http\Controllers\Admin\WatchdogController@export')->name('watchdog.export');
});

// 遊戲平台
Route::group(['prefix' => 'game', 'middleware' => [ 'auth' ] ], function () {
    //教師遊戲介面
    Route::get('/', 'App\Http\Controllers\Game\GameController@index')->name('game');
    Route::post('lock', 'App\Http\Controllers\Game\GameController@lock')->name('game.lock');
    Route::get('health', 'App\Http\Controllers\Game\GameController@health')->name('game.health');
    Route::get('roster/{room_id}', 'App\Http\Controllers\Game\GameController@classroom')->name('game.room');
    Route::post('character/fastedit', 'App\Http\Controllers\Game\GameController@fast_update')->name('game.character_edit');
    Route::post('character/skills', 'App\Http\Controllers\Game\GameController@get_skills')->name('game.get_skills');
    Route::post('character/items', 'App\Http\Controllers\Game\GameController@get_items')->name('game.get_items');
    Route::post('character/teammate', 'App\Http\Controllers\Game\GameController@get_teammate')->name('game.get_teammate');
    Route::post('character/uuid', 'App\Http\Controllers\Game\GameController@get_character')->name('game.get_character');
    Route::post('character/skill/cast', 'App\Http\Controllers\Game\GameController@skill_cast')->name('game.skill_cast');
    Route::post('character/item/use', 'App\Http\Controllers\Game\GameController@item_use')->name('game.item_use');
    Route::post('absent', 'App\Http\Controllers\Game\GameController@absent')->name('game.absent');
    Route::post('absent/auto', 'App\Http\Controllers\Game\GameController@auto_absent')->name('game.auto_absent');
    Route::post('positive/act', 'App\Http\Controllers\Game\GameController@positive_act')->name('game.positive_act');
    Route::post('negative/act', 'App\Http\Controllers\Game\GameController@negative_act')->name('game.negative_act');
    Route::post('negative/delay', 'App\Http\Controllers\Game\GameController@negative_delay')->name('game.negative_delay');
    Route::get('negative/regress/{delay_id}', 'App\Http\Controllers\Game\GameController@regress')->name('game.regress');
    Route::get('pickup/{room_id}', 'App\Http\Controllers\Game\GameController@pickup');
    Route::post('pickup/{room_id}', 'App\Http\Controllers\Game\GameController@random_pickup')->name('game.pickup');
    Route::get('timer/{room_id}', 'App\Http\Controllers\Game\GameController@timer')->name('game.timer');
    Route::get('silence/{room_id}', 'App\Http\Controllers\Game\GameController@silence')->name('game.silence');
    //系統管理介面
    Route::get('classes', 'App\Http\Controllers\Game\ClassController@index')->name('game.classes');
    Route::get('classes/add', 'App\Http\Controllers\Game\ClassController@add');
    Route::post('classes/add', 'App\Http\Controllers\Game\ClassController@insert')->name('game.class_add');
    Route::get('classes/edit/{class_id}', 'App\Http\Controllers\Game\ClassController@edit');
    Route::post('classes/edit/{class_id}', 'App\Http\Controllers\Game\ClassController@update')->name('game.class_edit');
    Route::post('classes/remove/{class_id}', 'App\Http\Controllers\Game\ClassController@remove')->name('game.class_remove');
    Route::get('classes/images/{class_id?}', 'App\Http\Controllers\Game\ClassController@gallery')->name('game.class_images');
    Route::get('classes/scan/{class_id}', 'App\Http\Controllers\Game\ClassController@scan')->name('game.class_scanimages');
    Route::post('classes/upload/{class_id}', 'App\Http\Controllers\Game\ClassController@store')->name('game.class_upload');
    Route::post('classes/destroy', 'App\Http\Controllers\Game\ClassController@destroy')->name('game.class_removeimage');
    Route::get('classes/faces/edit/{class_id?}', 'App\Http\Controllers\Game\ClassController@faces')->name('game.class_faces');
    Route::post('classes/faces/upload/{image_id}', 'App\Http\Controllers\Game\ClassController@face_upload')->name('game.face_upload');
    Route::get('classes/skills/{class_id?}', 'App\Http\Controllers\Game\ClassController@skills');
    Route::post('classes/skills/{class_id?}', 'App\Http\Controllers\Game\ClassController@skills_update')->name('game.class_skills');
    Route::get('skills', 'App\Http\Controllers\Game\SkillController@index')->name('game.skills');
    Route::get('skills/add', 'App\Http\Controllers\Game\SkillController@add');
    Route::post('skills/add', 'App\Http\Controllers\Game\SkillController@insert')->name('game.skill_add');
    Route::get('skills/edit/{skill_id}', 'App\Http\Controllers\Game\SkillController@edit');
    Route::post('skills/edit/{skill_id}', 'App\Http\Controllers\Game\SkillController@update')->name('game.skill_edit');
    Route::post('skills/remove/{skill_id}', 'App\Http\Controllers\Game\SkillController@remove')->name('game.skill_remove');
    Route::get('bases', 'App\Http\Controllers\Game\BaseController@index')->name('game.bases');
    Route::get('bases/add', 'App\Http\Controllers\Game\BaseController@add');
    Route::post('bases/add', 'App\Http\Controllers\Game\BaseController@insert')->name('game.base_add');
    Route::get('bases/edit/{base_id}', 'App\Http\Controllers\Game\BaseController@edit');
    Route::post('bases/edit/{base_id}', 'App\Http\Controllers\Game\BaseController@update')->name('game.base_edit');
    Route::post('bases/remove/{base_id}', 'App\Http\Controllers\Game\BaseController@remove')->name('game.base_remove');
    Route::get('furnitures', 'App\Http\Controllers\Game\FurnitureController@index')->name('game.furnitures');
    Route::get('furnitures/add', 'App\Http\Controllers\Game\FurnitureController@add');
    Route::post('furnitures/add', 'App\Http\Controllers\Game\FurnitureController@insert')->name('game.furniture_add');
    Route::get('furnitures/edit/{furniture_id}', 'App\Http\Controllers\Game\FurnitureController@edit');
    Route::post('furnitures/edit/{furniture_id}', 'App\Http\Controllers\Game\FurnitureController@update')->name('game.furniture_edit');
    Route::post('furnitures/remove/{furniture_id}', 'App\Http\Controllers\Game\FurnitureController@remove')->name('game.furniture_remove');
    Route::get('items', 'App\Http\Controllers\Game\ItemController@index')->name('game.items');
    Route::get('items/add', 'App\Http\Controllers\Game\ItemController@add');
    Route::post('items/add', 'App\Http\Controllers\Game\ItemController@insert')->name('game.item_add');
    Route::get('items/edit/{item_id}', 'App\Http\Controllers\Game\ItemController@edit');
    Route::post('items/edit/{item_id}', 'App\Http\Controllers\Game\ItemController@update')->name('game.item_edit');
    Route::post('items/remove/{item_id}', 'App\Http\Controllers\Game\ItemController@remove')->name('game.item_remove');
    Route::get('monsters', 'App\Http\Controllers\Game\MonsterController@index')->name('game.monsters');
    Route::get('monsters/add', 'App\Http\Controllers\Game\MonsterController@add');
    Route::post('monsters/add', 'App\Http\Controllers\Game\MonsterController@insert')->name('game.monster_add');
    Route::get('monsters/edit/{monster_id}', 'App\Http\Controllers\Game\MonsterController@edit');
    Route::post('monsters/edit/{monster_id}', 'App\Http\Controllers\Game\MonsterController@update')->name('game.monster_edit');
    Route::post('monsters/remove/{monster_id}', 'App\Http\Controllers\Game\MonsterController@remove')->name('game.monster_remove');
    Route::get('monsters/images/{monster_id?}', 'App\Http\Controllers\Game\MonsterController@gallery')->name('game.monster_images');
    Route::get('monsters/scan/{monster_id}', 'App\Http\Controllers\Game\MonsterController@scan')->name('game.monster_scanimages');
    Route::post('monsters/upload/{monster_id}', 'App\Http\Controllers\Game\MonsterController@store')->name('game.monster_upload');
    Route::post('monsters/destroy', 'App\Http\Controllers\Game\MonsterController@destroy')->name('game.monster_removeimage');
    Route::get('monsters/faces/edit/{monster_id?}', 'App\Http\Controllers\Game\MonsterController@faces')->name('game.monster_faces');
    Route::post('monsters/faces/upload/{image_id}', 'App\Http\Controllers\Game\MonsterController@face_upload')->name('game.monster_faceupload');
    Route::get('monsters/skills/{monster_id?}', 'App\Http\Controllers\Game\MonsterController@skills');
    Route::post('monsters/skills/{monster_id?}', 'App\Http\Controllers\Game\MonsterController@skills_update')->name('game.monster_skills');
    Route::get('maps', 'App\Http\Controllers\Game\MapController@gallery')->name('game.maps');
    Route::get('maps/scan', 'App\Http\Controllers\Game\MapController@scan')->name('game.map_scanimages');
    Route::post('maps/upload', 'App\Http\Controllers\Game\MapController@store')->name('game.map_upload');
    Route::post('maps/destroy', 'App\Http\Controllers\Game\MapController@destroy')->name('game.map_removeimage');
    //教師管理介面
    Route::get('rules/positive', 'App\Http\Controllers\Game\SettingsController@positive')->name('game.positive');
    Route::get('rules/negative', 'App\Http\Controllers\Game\SettingsController@negative')->name('game.negative');
    Route::get('rules/positive/add', 'App\Http\Controllers\Game\SettingsController@positive_add')->name('game.positive_add');
    Route::get('rules/negative/add', 'App\Http\Controllers\Game\SettingsController@negative_add')->name('game.negative_add');
    Route::post('rules/save', 'App\Http\Controllers\Game\SettingsController@insert')->name('game.rule_insert');
    Route::get('rules/edit/{rule_id}', 'App\Http\Controllers\Game\SettingsController@edit');
    Route::post('rules/edit/{rule_id}', 'App\Http\Controllers\Game\SettingsController@update')->name('game.rule_edit');
    Route::post('rules/remove/{rule_id}', 'App\Http\Controllers\Game\SettingsController@remove')->name('game.rule_remove');
    Route::get('evaluates', 'App\Http\Controllers\Game\SettingsController@evaluates')->name('game.evaluates');
    Route::get('evaluate/add', 'App\Http\Controllers\Game\SettingsController@evaluate_add');
    Route::post('evaluate/add', 'App\Http\Controllers\Game\SettingsController@evaluate_insert')->name('game.evaluate_add');
    Route::get('evaluate/edit/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@evaluate_edit');
    Route::post('evaluate/edit/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@evaluate_update')->name('game.evaluate_edit');
    Route::post('evaluate/remove/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@evaluate_remove')->name('game.evaluate_remove');
    Route::get('evaluate/manage/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@evaluate_manage')->name('game.evaluate_manage');
    Route::post('question/add', 'App\Http\Controllers\Game\SettingsController@question_insert')->name('game.question_add');
    Route::post('question/edit', 'App\Http\Controllers\Game\SettingsController@question_update')->name('game.question_edit');
    Route::post('question/remove', 'App\Http\Controllers\Game\SettingsController@question_remove')->name('game.question_remove');
    Route::post('question/answer', 'App\Http\Controllers\Game\SettingsController@question_answer')->name('game.question_answer');
    Route::post('option/add', 'App\Http\Controllers\Game\SettingsController@option_insert')->name('game.option_add');
    Route::post('option/edit', 'App\Http\Controllers\Game\SettingsController@option_update')->name('game.option_edit');
    Route::post('option/remove', 'App\Http\Controllers\Game\SettingsController@option_remove')->name('game.option_remove');
    Route::get('evaluate/assign/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@evaluate_assign')->name('game.evaluate_assign');
    Route::get('dungeon/add/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@dungeon_add');
    Route::post('dungeon/add/{evaluate_id}', 'App\Http\Controllers\Game\SettingsController@dungeon_insert')->name('game.dungeon_add');
    Route::get('dungeon/edit/{dungeon_id}', 'App\Http\Controllers\Game\SettingsController@dungeon_edit');
    Route::post('dungeon/edit/{dungeon_id}', 'App\Http\Controllers\Game\SettingsController@dungeon_update')->name('game.dungeon_edit');
    Route::post('dungeon/remove/{dungeon_id}', 'App\Http\Controllers\Game\SettingsController@dungeon_remove')->name('game.dungeon_remove');
    //班級管理介面
    Route::get('configure', 'App\Http\Controllers\Game\ClassroomController@config');
    Route::post('configure', 'App\Http\Controllers\Game\ClassroomController@save_config')->name('game.classroom_config');
    Route::get('groups', 'App\Http\Controllers\Game\ClassroomController@regroup')->name('game.regroup');
    Route::post('group/change', 'App\Http\Controllers\Game\ClassroomController@change_group')->name('game.change_party');
    Route::get('group/add', 'App\Http\Controllers\Game\ClassroomController@party_add');
    Route::post('group/add', 'App\Http\Controllers\Game\ClassroomController@party_insert')->name('game.party_add');
    Route::get('group/edit/{party_id}', 'App\Http\Controllers\Game\ClassroomController@party_edit');
    Route::post('group/edit/{party_id}', 'App\Http\Controllers\Game\ClassroomController@party_update')->name('game.party_edit');
    Route::post('group/remove/{party_id}', 'App\Http\Controllers\Game\ClassroomController@party_remove')->name('game.party_remove');
    Route::get('classroom/characters', 'App\Http\Controllers\Game\ClassroomController@characters')->name('game.characters');
    Route::get('classroom/setup/{uuid}', 'App\Http\Controllers\Game\ClassroomController@character_edit');
    Route::post('classroom/setup/{uuid}', 'App\Http\Controllers\Game\ClassroomController@character_class')->name('game.profession_setup');
    Route::get('classroom/setup/image/{uuid}', 'App\Http\Controllers\Game\ClassroomController@image_edit');
    Route::post('classroom/setup/image/{uuid}', 'App\Http\Controllers\Game\ClassroomController@character_image')->name('game.image_setup');
    Route::get('dungeons', 'App\Http\Controllers\Game\ClassroomController@dungeons')->name('game.dungeons');
    Route::get('dungeons/answers/{dungeon_id}', 'App\Http\Controllers\Game\ClassroomController@answers')->name('game.answers');
    Route::post('dungeons/answers/remove/{answer_id}', 'App\Http\Controllers\Game\ClassroomController@answer_remove')->name('game.answer_remove');
    Route::get('dungeons/answers/journeys/{answer_id}', 'App\Http\Controllers\Game\ClassroomController@journeys')->name('game.journeys');
    Route::get('classroom/reset', 'App\Http\Controllers\Game\ClassroomController@reset');
    Route::post('classroom/reset', 'App\Http\Controllers\Game\ClassroomController@do_reset')->name('game.reset');
    //學生初始設定
    Route::get('profession', 'App\Http\Controllers\Game\PlayerController@character_edit');
    Route::post('profession', 'App\Http\Controllers\Game\PlayerController@character_class')->name('game.player_profession');
    Route::get('image', 'App\Http\Controllers\Game\PlayerController@image_edit');
    Route::post('image', 'App\Http\Controllers\Game\PlayerController@character_image')->name('game.player_image');
    //學生遊戲介面
    Route::group(['prefix' => 'player', 'middleware' => [ 'student', 'noprofession', 'noimage' ] ], function () {
        Route::get('/', 'App\Http\Controllers\Game\PlayerController@index')->name('game.player');
        Route::get('party', 'App\Http\Controllers\Game\PlayerController@party')->name('game.party');
        Route::get('arena', 'App\Http\Controllers\Game\PlayerController@arena')->name('game.arena');
        Route::get('dungeon', 'App\Http\Controllers\Game\PlayerController@dungeon')->name('game.dungeon');
        Route::get('shop/furniture', 'App\Http\Controllers\Game\PlayerController@furniture_shop')->name('game.furniture_shop');
        Route::get('shop/item', 'App\Http\Controllers\Game\PlayerController@item_shop')->name('game.item_shop');
        Route::post('talkto', 'App\Http\Controllers\Game\MessagerController@personal')->name('game.private');
        Route::post('partytalk', 'App\Http\Controllers\Game\MessagerController@party')->name('game.party_channel');
        Route::post('broadcast', 'App\Http\Controllers\Game\MessagerController@classroom')->name('game.room_channel');
        Route::post('skills/scan', 'App\Http\Controllers\Game\PlayerController@get_skills')->name('game.get_myskills');
        Route::post('items/scan', 'App\Http\Controllers\Game\PlayerController@get_items')->name('game.get_myitems');
        Route::post('furnitures/scan', 'App\Http\Controllers\Game\PlayerController@get_furnitures')->name('game.get_myfurnitures');
        Route::post('party/name', 'App\Http\Controllers\Game\PlayerController@party_name')->name('game.party_name');
        Route::post('party/desc', 'App\Http\Controllers\Game\PlayerController@party_desc')->name('game.party_desc');
        Route::post('party/leader', 'App\Http\Controllers\Game\PlayerController@party_leader')->name('game.party_leader');
        Route::post('party/base', 'App\Http\Controllers\Game\PlayerController@party_base')->name('game.party_base');
        Route::post('donate/cash', 'App\Http\Controllers\Game\PlayerController@donate')->name('game.donate');
        Route::post('donate/item', 'App\Http\Controllers\Game\PlayerController@given')->name('game.given');
        Route::post('furniture/buy', 'App\Http\Controllers\Game\PlayerController@buy_furniture')->name('game.buy_furniture');
        Route::post('furniture/sell', 'App\Http\Controllers\Game\PlayerController@sell_furniture')->name('game.sell_furniture');
        Route::post('item/buy', 'App\Http\Controllers\Game\PlayerController@buy_item')->name('game.buy_item');
        Route::post('item/sell', 'App\Http\Controllers\Game\PlayerController@sell_item')->name('game.sell_item');
        Route::post('arena/refresh', 'App\Http\Controllers\Game\PlayerController@refresh_arena')->name('game.refresh_arena');
        Route::post('arena/broadcast', 'App\Http\Controllers\Game\PlayerController@come_arena')->name('game.come_arena');
        Route::post('arena/battle/ask', 'App\Http\Controllers\Game\PlayerController@invite_battle')->name('game.invite_battle');
        Route::post('arena/battle/accept', 'App\Http\Controllers\Game\PlayerController@accept_battle')->name('game.accept_battle');
        Route::post('arena/battle/reject', 'App\Http\Controllers\Game\PlayerController@reject_battle')->name('game.reject_battle');
        Route::post('dungeon/list', 'App\Http\Controllers\Game\PlayerController@get_dungeons')->name('game.get_dungeons');
        Route::post('dungeon/enter', 'App\Http\Controllers\Game\PlayerController@enter_dungeon')->name('game.enter_dungeon');
        Route::post('dungeon/exit', 'App\Http\Controllers\Game\PlayerController@exit_dungeon')->name('game.exit_dungeon');
        Route::post('dungeon/journey', 'App\Http\Controllers\Game\PlayerController@journey')->name('game.journey');
        Route::post('monster/respawn', 'App\Http\Controllers\Game\PlayerController@monster_respawn')->name('game.monster_respawn');
        Route::post('monster/attack', 'App\Http\Controllers\Game\PlayerController@monster_attack')->name('game.monster_attack');
        Route::post('monster/skill', 'App\Http\Controllers\Game\PlayerController@skill_monster')->name('game.skill_monster');
        Route::post('monster/item', 'App\Http\Controllers\Game\PlayerController@item_monster')->name('game.item_monster');
    });
});

