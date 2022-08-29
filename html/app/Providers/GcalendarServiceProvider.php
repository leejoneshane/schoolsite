<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\IcsCalendar;
use App\Models\IcsEvent;

class GcalendarServiceProvider extends ServiceProvider
{

	private $calendar = null;

    public function __construct()
    {
        if (is_null($this->calendar)) {
			$this->init();
		}
    }

	public function init()
	{
		$path = config('services.gsuite.auth_config');
		$user_to_impersonate = config('services.gsuite.calendar');
		$scopes = [
            \Google_Service_Calendar::CALENDAR,
            \Google_Service_Calendar::CALENDAR_EVENTS,		];

		$client = new \Google_Client();
		$client->setAuthConfig($path);
		$client->setApplicationName('School Web Site');
		$client->setScopes($scopes);
		$client->setSubject($user_to_impersonate);
		try {
			$this->calendar = new \Google_Service_Calendar($client);
		} catch (\Google_Service_Exception $e) {
			Log::error('google calendar:' . $e->getMessage());
		}
	}
	public function list_calendars()
	{
		try {
			$cals = $this->calendar->calendarList->listCalendarList()->getItems();
			DB::table('ics_calendars')->truncate();
			foreach ($cals as $cal) {
				$ics = IcsCalendar::firstOrNew(['id' => $cal->getId()]);
				$ics->summary = $cal->getSummary();
				$ics->save();
			}
			return $cals;
		} catch (\Google_Service_Exception $e) {
			Log::notice('google listCalendars:' . $e->getMessage());
			return false;
		}
	}
	
	public function get_calendar($calendarId)
	{
		try {
			return $this->calendar->calendars->get($calendarId);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google getCalendar($calendarId):" . $e->getMessage());
			return false;
		}
	}
	
	public function delete_calendar($calendarId)
	{
		try {
			$this->calendar->calendars->delete($calendarId);
			return true;
		} catch (\Google_Service_Exception $e) {
			Log::notice("google deleteCalendar($calendarId):" . $e->getMessage());
			return false;
		}
	}
	
	public function create_calendar($description)
	{
		$cObj = new \Google_Service_Calendar_Calendar();
		$cObj->setSummary($description);
		$cObj->setTimeZone('Asia/Taipei');
		try {
			$created = $this->calendar->calendars->insert($cObj);
			return $created->getId();
		} catch (\Google_Service_Exception $e) {
			Log::notice('google createCalendar('.var_export($cObj, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function update_calendar($calendarId, $description)
	{
		$cObj = $this->get_calendar($calendarId);
		$cObj->setSummary($description);
		try {
			$this->calendar->calendars->update($calendarId, $cObj);
			return true;
		} catch (\Google_Service_Exception $e) {
			Log::notice("google updateCalendar($calendarId,".var_export($cObj, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function prune_events($calendarId)
	{
		try {
			$this->calendar->calendars->clear($calendarId);
			return true;
		} catch (\Google_Service_Exception $e) {
			Log::notice("google pruneEvents($calendarId):" . $e->getMessage());
			return false;
		}
	}
	
	public function list_events($calendarId, $opt_param = null)
	{
		if (empty($calendarId)) {
			$calendarId = 'primary';
		}
		if (empty($opt_param)) {
			$mydate = $this->current_seme();
			$opt_param['timeMin'] = $mydate['min'];
			$opt_param['timeMax'] = $mydate['max'];
			$opt_param['singleEvents'] = true;
			$opt_param['orderBy'] = 'startTime';
		}
		try {
			$events = $this->calendar->events->listEvents($calendarId, $opt_param);
			return $events->getItems();
		} catch (\Google_Service_Exception $e) {
			Log::notice("google listEvents($calendarId):" . $e->getMessage());
			return false;
		}
	}
	
	public function get_event($calendarId, $eventId)
	{
		try {
			return $this->calendar->events->get($calendarId, $eventId);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google getEvent($calendarId, $eventId):" . $e->getMessage());
			return false;
		}
	}
	
	public function move_event($calendarId, $eventId, $target)
	{
		try {
			return $this->calendar->events->move($calendarId, $eventId, $target);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google moveEvent($calendarId, $eventId, $target):" . $e->getMessage());
			return false;
		}
	}
	
	public function delete_event($calendarId, $eventId)
	{
		try {
			$this->calendar->events->delete($calendarId, $eventId);
			return true;
		} catch (\Google_Service_Exception $e) {
			Log::notice("google deleteEvent($calendarId, $eventId):" . $e->getMessage());
			return false;
		}
	}
	
	public function create_event($calendarId, \Google_Service_Calendar_Event $event)
	{
		try {
			return $this->calendar->events->insert($calendarId, $event);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google createEvent($calendarId,".var_export($event, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function import_event($calendarId, \Google_Service_Calendar_Event $event)
	{
		try {
			return $this->calendar->events->import($calendarId, $event);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google importEvent($calendarId,".var_export($event, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function update_event($calendarId, $eventId, \Google_Service_Calendar_Event $event)
	{
		try {
			$updatedEvent = $this->calendar->events->update($calendarId, $eventId, $event);
			return $updatedEvent->getUpdated();
		} catch (\Google_Service_Exception $e) {
			Log::notice("google updateEvent($calendarId,$eventId,".var_export($event, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function sync_event(IcsEvent $ics)
	{
		$calendar_id = $ics->calendar_id;
		$event_id = $ics->event_id;
		if (!is_null($event_id)) {
			$event = $this->get_event($calendar_id, $event_id);
			if (!$event) {
				$event = new \Google_Service_Calendar_Event();
			} else {
				if ($event->getStatus() == 'cancelled') {
					$event->setStatus('confirmed');
				}
			}
		} else {
			$event = new \Google_Service_Calendar_Event();
		}
		$event->setSummary($ics->summary);
		if (!empty($ics->description)) {
			$event->setDescription($ics->description);
		}
		if (!empty($ics->location)) {
			$event->setLocation($ics->location);
		}
		$organizer = new \Google_Service_Calendar_EventOrganizer();
		$organizer->setEmail(config('services.gsuite.calendar'));
		$organizer->setDisplayName($ics->unit->name);
		$event->setOrganizer($organizer);
		$period = CarbonPeriod::create($ics->startDate, $ics->endDate);
		foreach ($period as $date) {
			$event_start = new \Google_Service_Calendar_EventDateTime();
			$event_end = new \Google_Service_Calendar_EventDateTime();
			$event_start->setTimeZone(env('TZ'));
			$event_end->setTimeZone(env('TZ'));
			if ($ics->all_day) {
				$event_start->setDate($date->format('Y-m-d'));
				$event_end->setDate($date->format('Y-m-d'));
			} else {
				$event_start->setDateTime($date->format('Y-m-d').' '.$ics->startTime);
				$event_end->setDateTime($date->format('Y-m-d').' '.$ics->endTime);
			}
			$event->setStart($event_start);
			$event->setEnd($event_end);	
		}
		if (!empty($event_id)) {
			$event = $this->update_event($calendar_id, $event_id, $event);
		} else {
			$event = $this->create_event($calendar_id, $event);
		}
		if ($event instanceof \Google_Service_Calendar_Event) {
			$ics->event_id = $event->getId();
			$ics->save();
		}
	
		return $event;
	}

	public static function current_seme()
	{
		if (date('m') > 7) {
			$syear = date('Y');
			$eyear = $syear + 1;
			$gyear = $syear - 1911;
			$seme = 1;
			$min = "$syear-08-01T00:00:00+08:00";
			$max = "$eyear-01-31T00:00:00+08:00";
		} elseif (date('m') < 2) {
			$eyear = date('Y');
			$syear = $eyear - 1;
			$gyear = $syear - 1911;
			$seme = 1;
			$min = "$syear-08-01T00:00:00+08:00";
			$max = "$eyear-01-31T00:00:00+08:00";
		} else {
			$syear = date('Y');
			$eyear = $syear;
			$gyear = $syear - 1912;
			$seme = 2;
			$min = "$syear-02-01T00:00:00+08:00";
			$max = "$eyear-07-31T00:00:00+08:00";
		}
	
		return ['min' => $min, 'max' => $max, 'syear' => $syear, 'eyear' => $eyear, 'gyear' => $gyear, 'seme' => $seme];
	}

}