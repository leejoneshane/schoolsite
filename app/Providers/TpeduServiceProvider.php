<?php

namespace App\Providers;

use Log;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TpeduServiceProvider extends ServiceProvider
{

	private static $oauth = null;
    private static $seme = null;
    private static $error = '';
    public $expires_in = '';
	public $access_token = '';
	public $refresh_token = '';

    public function __construct()
    {
        if (is_null(self::$oauth)) {
            self::$oauth = new Http([
                'verify' => false,
                'base_uri' => config('services.tpedu.server'),
            ]);
		}
        self::$seme = $this->current_seme();
    }

	public function current_seme()
	{
    	if (date('m') > 7) {
        	$year = date('Y') - 1911;
        	$seme = 1;
    	} elseif (date('m') < 2) {
        	$year = date('Y') - 1912;
        	$seme = 1;
    	} else {
        	$year = date('Y') - 1912;
        	$seme = 2;
    	}

    	return ['year' => $year, 'seme' => $seme];
	}

	public function is_phone($str)
	{
    	if (preg_match('/^09[0-9]{8}$/', $str)) {
        	return true;
    	} else {
        	return false;
    	}
	}

	public function get_tokens($auth_code)
	{
		$response = self::$oauth->post(config('services.tpedu.endpoint.token'), [
			'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ],
			'form_params' => [
				'grant_type' => 'authorization_code',
				'client_id' => config('services.tpedu.app'),
				'client_secret' => config('services.tpedu.secret'),
				'redirect_uri' => config('services.tpedu.callback'),
				'code' => $auth_code,
			],
		]);
		$data = json_decode($response->getBody());
		if ($response->getStatusCode() == 200) {
			self::$expires_in = time() + $data->expires_in;
			self::$access_token = $data->access_token;
			self::$refresh_token = $data->refresh_token;
		} else {
			Log::notice('oauth2 token response =>'.$response->getBody());	
			return false;
		}
	}

	public function refresh_tokens()
	{
    	if (self::$refresh_token && self::$expires_in < time()) {
        	$response = self::$oauth->post(config('services.tpedu.endpoint.token'), [
            	'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ],
            	'form_params' => [
                	'grant_type' => 'refresh_token',
                	'client_id' => config('services.tpedu.app'),
                	'client_secret' => config('services.tpedu.secret'),
                	'refresh_token' => self::$refresh_token,
                	'scope' => 'user',
            	],
        	]);
        	$data = json_decode($response->getBody());
        	if ($response->getStatusCode() == 200) {
            	self::$expires_in = time() + $data->expires_in;
            	self::$access_token = $data->access_token;
            	self::$refresh_token = $data->refresh_token;
        	} else {
            	Log::notice('oauth2 token response =>'.$response->getBody());
            	return false;
        	}
    	}
	}

	public function login()
    {
		if (empty(self::$access_token)) {
			return redirect()->away(
				config('services.tpedu.server') . '/' .
				config('services.tpedu.login') . '?' .
				'client_id' . '=' . config('services.tpedu.app') . '&' .
				'redirect_uri' . '=' . config('services.tpedu.callback') . '&' .
				'response_type=code' . '&' .
				'scope=user'
			);
		}
	}

	public function who()
	{
		if (self::$access_token) {
			$response = self::$oauth->get(config('services.tpedu.endpoint.user'), [
				'headers' => [ 'Authorization' => 'Bearer '.self::$access_token ],
			]);
			$user = json_decode($response->getBody());
			if ($response->getStatusCode() == 200) {
				return User::where('uuid', $user->uuid)->first();
			} else {
				Log::error('oauth2 user response =>'.$response->getBody());
				return false;
			}
		}
		return false;
	}

	public function profile()
	{
		if (self::$access_token) {
			$response = self::$oauth->get(config('services.tpedu.endpoint.profile'), [
				'headers' => [ 'Authorization' => 'Bearer ' . self::$access_token ],
			]);
			$user = json_decode($response->getBody());
			if ($response->getStatusCode() == 200) {
				if ($user->employee_type == '學生') {
					return Student::find($user->uuid);
				} else {
					return Teacher::find($user->uuid);					
				}
			} else {
				Log::error('oauth2 profile response =>'.$response->getBody());
				return false;
			}
		}
		return false;
	}

	public function api($which, array $replacement = [])
	{
		if (empty(self::$access_token)) return;
		$dataapi = config('services.tpedu.endpoint.' . $which);
    	if ($which == 'find_users') {
			if (!empty($replacement)) {
				$dataapi .= '?';
				foreach ($replacement as $key => $data) {
					$dataapi .= $key.'='.$data.'&';
				}
				$dataapi = substr($dataapi, 0, -1);
			}
			$dataapi = str_replace('{school}', config('services.tpedu.school'), $dataapi);
		} else {
			$replacement['school'] = config('services.tpedu.school');
			$search = [];
			$values = [];
			foreach ($replacement as $key => $data) {
				$search[] = '{'.$key.'}';
				$values[] = $data;
			}
			$dataapi = str_replace($search, $values, $dataapi);
		}
		$response = self::$oauth->get($dataapi, [
			'headers' => ['Authorization' => 'Bearer ' . config('services.tpedu.token')],
			'http_errors' => false,
		]);
		$json = json_decode($response->getBody());
		if ($response->getStatusCode() == 200) {
			return $json;
		} else {
			Log::error('oauth2 api('.$dataapi.') response:'.$dataapi.'=>'.$response->getBody());
			return false;
		}
	}

	function sync_teachers()
	{
    	$uuids = api('all_teachers');
    	if ($uuids && is_array($uuids)) {
        	foreach ($uuids as $uuid) {
            	fetch_user($uuid);
        	}
    	}
	}

	function sync_students($cls)
	{
    	$uuids = api('students_of_class', ['cls' => $cls]);
    	if ($uuids && is_array($uuids)) {
        	foreach ($uuids as $uuid) {
            	fetch_user($uuid);
        	}
    	}
	}

	public function fetch_user($uuid)
	{
		$user = api('one_user', ['uuid' => $uuid]);
		if ($user) {
			if (is_array($user->uid)) {
				foreach ($user->uid as $u) {
					if (!strpos($u, '@') && !is_phone($u)) {
						$account = $u;
					}
				}
			} else {
				$account = $user->uid;
			}
			$m_dept_id = '';
			$m_dept_name = '';
			$m_role_id = '';
			$m_role_name = '';
			$stu = ($user->employeeType == '學生') ? true : false; 
			if ($stu) {
				$myclass = $user->tpClass;
				$myseat = $user->tpSeat;
				$m_dept_id = $myclass;
				$m_dept_name = $myclass;
				if (isset($user->tpClassTitle)) {
					$m_dept_name = $user->tpClassTitle;
				}
				$m_role_id = $m_dept_id;
				$m_role_name = $m_dept_name;
			} else {
				DB::table('job_title')->where('uuid', $uuid)->dalete();
				DB::table('assignment')->where('uuid', $uuid)->delete();	
				if (isset($user->ou) && isset($user->title)) {
					$sdept = config('sub_dept');
					$keywords = explode(',', $sdept);
					if (is_array($user->ou)) {
						foreach ($user->ou as $ou_pair) {
							$a = explode(',', $ou_pair);
							$o = $a[0];
							$dept_name = get_unit_name($a[1]);
							$ckf = 0;
							foreach ($keywords as $k) {
								if (!(mb_strpos($dept_name, $k) === false)) {
									$ckf = 1;
								}
							}
							if (!$ckf || ($m_dept_id == '' && $ckf)) {
								$m_dept_id = $a[1];
								$m_dept_name = $dept_name;
							}
						}
					} else {
						$a = explode(',', $user->ou);
						$o = $a[0];
						$m_dept_id = $a[1];
						$d = $user->department->{$o}[0];
						$m_dept_name = $d->name;
					}
					if (is_array($user->title)) {
						foreach ($user->title as $ro_pair) {
							$a = explode(',', $ro_pair);
							$o = $a[0];
							$role_name = get_role_name($a[2]);
							$ckf = 0;
							foreach ($keywords as $k) {
								if (!(mb_strpos($role_name, $k) === false)) {
									$ckf = 1;
								}
							}
							if (!$ckf || ($m_dept_id == '' && $ckf)) {
								$m_role_id = $a[2];
								$m_role_name = $role_name;
							}
							DB::table('job_title')->insert([
								'uuid' => $uuid,
								'dept_id' => $a[1],
								'role_id' => $a[2],
							]);
						}
					} else {
						$a = explode(',', $user->title);
						$o = $a[0];
						$m_role_id = $a[1];
						$d = $user->titleName->$o[0];
						$m_role_name = $d->name;
						DB::table(job_title)->insert([
							'uuid' => $uuid,
							'dept_id' => $a[1],
							'role_id' => $a[2],
						]);
					}
				}
				if (!empty($user->tpTutorClass)) {
					$tclass = $user->tpTutorClass;
				}
				if (isset($user->tpTeachClass)) {
					foreach ($user->tpTeachClass as $assign_pair) {
						$a = explode(',', $assign_pair);
						DB::table('assignment')->insert([
							'uuid' => $uuid,
							'class_id' => $a[1],
							'subject_id' => $a[2],
						]);
					}
				}	
			}
			$fields = [
				'uuid' => $uuid,
				'idno' => $user->cn,
				'id' => $user->employeeNumber,
				'student' => $stu,
				'account' => $account,
				'sn' => $user->sn,
				'gn' => $user->givenName,
				'realname' => $user->displayName,
				'dept_id' => $m_dept_id,
				'dept_name' => $m_dept_name,
				'role_id' => $m_role_id,
				'role_name' => $m_role_name,
				'birthdate' => date('Y-m-d H:i:s', strtotime($user->birthDate)),
				'gender' => $user->gender,
				'status' => $user->inetUserStatus,
				'fetch_date' => date('Y-m-d H:i:s'),
			];
			if (!empty($user->mobile)) {
				$fields['mobile'] = $user->mobile;
			}
			if (!empty($user->telephoneNumber)) {
				$fields['telephone'] = $user->telephoneNumber;
			}
			if (!empty($user->homePhone)) {
				$fields['telephone'] = $user->homePhone;
			}
			if (!empty($user->registeredAddress)) {
				$fields['address'] = $user->registeredAddress;
			}
			if (!empty($user->homePostalAddress)) {
				$fields['address'] = $user->homePostalAddress;
			}
			if (!empty($user->mail)) {
				$fields['email'] = preg_replace('/\s(?=)/', '', $user->mail);
			}
			if (!empty($user->wWWHomePage)) {
				$fields['www'] = $user->wWWHomePage;
			}
			if (!empty($tclass)) {
				$fields['tutor_class'] = $myclass;
			}
			if (!empty($myclass)) {
				$fields['class'] = $myclass;
			}
			if (!empty($myseat)) {
				$fields['seat'] = $myseat;
			}
			if (!empty($user->tpCharacter)) {
				if (is_array($user->tpCharacter)) {
					$fields['character'] = implode(',', $user->tpCharacter);
				} else {
					$fields['character'] = $user->tpCharacter;
				}
			}
			if ($stu) {
				DB::table('students')->updateOrInsert($fields);
			} else {
				DB::table('teachers')->updateOrInsert($fields);
			}
			return true;
		}
		return false;
	}

	function sync_units()
	{
		DB::table('units')->delete();
		$ous = api('all_units');
		if ($ous) {
			foreach ($ous as $o) {
				$fields = [
					'id' => $o->ou,
					'name' => $o->description,
				];
				DB::table('units')->updateOrInsert($fields);
			}
		}
	}
	function sync_roles()
	{
		DB::table('roles')->delete();
		$ous = api('all_units');
		if ($ous) {
			foreach ($ous as $o) {
				$roles = api('roles_of_unit', ['ou' => $o->ou]);
				if ($roles) {
					foreach ($roles as $r) {
						$fields = [
							'id' => $r->cn,
							'unit_id' => $o->ou,
							'name' => $r->description,
						];
						DB::table('roles')->updateOrInsert($fields);
					}
				}
			}
		}
	}
	function sync_subjects()
	{
		DB::table('subjects')->delete();
		$subjects = api('all_subjects');
		if ($subjects) {
			$n = 1;
			foreach ($subjects as $s) {
				$fields = [
					'id' => 'subj' . ($n<10) ? "0$n" : "$n",
					'name' => $s->description,
				];
				$n += 1;
				DB::table('subjects')->updateOrInsert($fields);
			}
		}
	}
	function sync_classes()
	{
		DB::table('classes')->delete();
		$classes = api('all_classes');
		if ($classes) {
			foreach ($classes as $c) {
				$fields = [
					'id' => $c->ou,
					'grade_id' => $c->grade,
					'name' => $c->description,
				];
				if (isset($c->tutor[0])) {
					$fields['tutor'] = $c->tutor[0];
				}
				DB::table('classes')->updateOrInsert($fields);
			}
		}
	}

}