<?php

namespace App\Providers;

use Log;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;

class TpeduServiceProvider extends ServiceProvider
{

    private $seme = null;
    private $error = '';
    public $expires_in = '';
	public $access_token = '';
	public $refresh_token = '';

    public function __construct()
    {
        $this->seme = $this->current_seme();
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
		$response = Http::baseUrl(config('services.tpedu.server')
		)->withHeaders([
			'Content-Type' => 'application/x-www-form-urlencoded'
		])->post(config('services.tpedu.endpoint.token'), [
			'grant_type' => 'authorization_code',
			'client_id' => config('services.tpedu.app'),
			'client_secret' => config('services.tpedu.secret'),
			'redirect_uri' => config('services.tpedu.callback'),
			'code' => $auth_code,
		]);
		$data = json_decode($response->getBody());
		if ($response->getStatusCode() == 200) {
			$this->expires_in = time() + $data->expires_in;
			$this->access_token = $data->access_token;
			$this->refresh_token = $data->refresh_token;
		} else {
			$this->error = $response->getBody();
			Log::error('oauth2 token response =>'.$this->error);
			return false;
		}
	}

	public function refresh_tokens()
	{
    	if ($this->refresh_token && $this->expires_in < time()) {
        	$response = Http::baseUrl(config('services.tpedu.server')
			)->withHeaders([
				'Content-Type' => 'application/x-www-form-urlencoded'
			])->post(config('services.tpedu.endpoint.token'), [
				'grant_type' => 'refresh_token',
				'client_id' => config('services.tpedu.app'),
				'client_secret' => config('services.tpedu.secret'),
				'refresh_token' => $this->refresh_token,
				'scope' => 'user',
        	]);
        	$data = json_decode($response->getBody());
        	if ($response->getStatusCode() == 200) {
            	$this->expires_in = time() + $data->expires_in;
            	$this->access_token = $data->access_token;
            	$this->refresh_token = $data->refresh_token;
        	} else {
				$this->error = $response->getBody();
				Log::error('oauth2 token response =>'.$this->error);
            	return false;
        	}
    	}
	}

	public function login()
    {
			return config('services.tpedu.server') . '/' .
				config('services.tpedu.login') . '?' .
				'client_id' . '=' . config('services.tpedu.app') . '&' .
				'redirect_uri' . '=' . config('services.tpedu.callback') . '&' .
				'response_type=code' . '&' .
				'scope=user' . '&' .
				'sub=laravel';
	}

	public function error()
	{
		return $this->error;
	}

	public function who()
	{
		if ($this->access_token) {
			$response = Http::baseUrl(config('services.tpedu.server')
			)->withHeaders([
				'Authorization' => 'Bearer '.$this->access_token,
			])->get(config('services.tpedu.endpoint.user'));
			$user = json_decode($response->getBody());
			if ($response->getStatusCode() == 200) {
				return User::where('uuid', $user->uuid)->first();
			} else {
				$this->error = $response->getBody();
				Log::error('oauth2 user response =>'.$this->error);
				return false;
			}
		}
		return false;
	}

	public function profile()
	{
		if ($this->access_token) {
			$response = Http::baseUrl(config('services.tpedu.server')
			)->withHeaders([
				'Authorization' => 'Bearer ' . $this->access_token,
			])->get(config('services.tpedu.endpoint.profile'));
			$user = json_decode($response->getBody());
			if ($response->getStatusCode() == 200) {
				if ($user->employee_type == '學生') {
					return Student::find($user->uuid);
				} else {
					return Teacher::find($user->uuid);					
				}
			} else {
				$this->error = $response->getBody();
				Log::error('oauth2 profile response =>'.$this->error);
				return false;
			}
		}
		return false;
	}

	public function api($which, array $replacement = [])
	{
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
		$response = Http::baseUrl(config('services.tpedu.server')
		)->withHeaders([
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . config('services.tpedu.token'),
		])->get($dataapi);
		if ($response->getStatusCode() == 200) {
			$json = json_decode($response->getBody());
			return $json;
		} else {
			$this->error = $response->getBody();
			Log::error('oauth2 '.$dataapi.' response =>'.$this->error);
			return false;
		}
	}

	function sync_teachers()
	{
    	$uuids = $this->api('all_teachers');
    	if ($uuids && is_array($uuids)) {
        	foreach ($uuids as $uuid) {
            	$this->fetch_user($uuid);
        	}
    	}
	}

	function sync_students($cls)
	{
    	$uuids = $this->api('students_of_class', ['class' => $cls]);
    	if ($uuids && is_array($uuids)) {
        	foreach ($uuids as $uuid) {
            	$this->fetch_user($uuid);
        	}
    	}
	}

	public function fetch_user($uuid)
	{
		$temp = Student::find($uuid);
		if ($temp) {
			if (!$temp->expired) return false;
		} else {
			$temp = Teacher::find($uuid);
			if ($temp) {
				$expire = new Carbon($temp->updated_at);
				if (!$temp->expired) return false;
			}
		}
		$o = config('services.tpedu.school');
		$user = $this->api('one_user', ['uuid' => $uuid]);
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
			$birth = date('Y-m-d', strtotime($user->birthDate));
			$stu = ($user->employeeType == '學生') ? true : false; 
			if ($stu) {
				$emp = Student::firstOrNew(['uuid' => $uuid]);
				$emp->class_id = $user->tpClass;
				$emp->seat = $user->tpSeat;
			} else {
				$emp = Teacher::firstOrNew(['uuid' => $uuid]);
				$m_dept_id = '';
				$m_dept_name = '';
				$m_role_id = '';
				$m_role_name = '';
				DB::table('job_title')->where('uuid', $uuid)->delete();
				DB::table('assignment')->where('uuid', $uuid)->delete();	
				if (isset($user->ou) && isset($user->title)) {
					$keywords = explode(',', config('services.tpedu.base_unit'));
					if (is_array($user->ou)) {
						foreach ($user->ou as $ou_pair) {
							$a = explode(',', $ou_pair);
							if ($a[0] == $o) {
								$dept_name = Unit::find($a[1])->name;
								$ckf = false;
								foreach ($keywords as $k) {
									if (!(mb_strpos($dept_name, $k) === false)) {
										$ckf = true;
									}
								}
								if (!$ckf || empty($m_dept_id)) {
									$m_dept_id = $a[1];
									$m_dept_name = $dept_name;
								}
							}
						}
					} else {
						$a = explode(',', $user->ou);
						if ($a[0] == $o) {
							$m_dept_id = $a[1];
							$dept_name = Unit::find($a[1])->name;
							$m_dept_name = $dept_name;	
						}
					}
					$emp->unit_id = $m_dept_id;
					$emp->unit_name = $m_dept_name;
					if (is_array($user->titleName->{$o})) {
						foreach ($user->titleName->{$o} as $ro) {
							$a = explode(',', $ro->key);
							if ($a[0] == $o) {
								$u = $a[1];
								$r = $a[2];
								$role_name = $ro->name; 
								$ckf = false;
								foreach ($keywords as $k) {
									if (!(mb_strpos($role_name, $k) === false)) {
										$ckf = true;
									}
								}
								$role = Role::where('role_no', $r)->where('unit_id', $u)->first();
								if (!$role) {
									$role = Role::create([
										'role_no' => $r,
										'unit_id' => $u,
										'name' => $role_name,
									]);	
								}
								if (!$ckf || empty($m_role_id)) {
									$m_role_id = $role->id;
									$m_role_name = $role_name;
								}
								DB::table('job_title')->insert([
									'uuid' => $uuid,
									'unit_id' => $u,
									'role_id' => $role->id,
								]);	
							}
						}
					} else {
						$ro = $user->titleName->{$o};
						$a = explode(',', $ro->key);
						if ($a[0] == $o) {
							$u = $a[1];
							$r = $a[2];
							$role_name = $ro->name; 
							$role = Role::where('role_no', $r)->where('unit_id', $u)->first();
							if (!$role) {
								$role = Role::create([
									'role_no' => $r,
									'unit_id' => $u,
									'name' => $role_name,
								]);	
							}
						$m_role_id = $role->id;
							$m_role_name = $role_name;
							DB::table('job_title')->insert([
								'uuid' => $uuid,
								'unit_id' => $u,
								'role_id' => $role->id,
							]);	
						}
					}
					$emp->role_id = $m_role_id;
					$emp->role_name = $m_role_name;
				}
				if (!empty($user->tpTutorClass)) {
					$emp->tutor_class = $user->tpTutorClass;
				}
				if (isset($user->tpTeachClass)) {
					foreach ($user->tpTeachClass as $assign_pair) {
						$a = explode(',', $assign_pair);
						$s = Subject::where('name', substr($a[2], 4))->first();
						DB::table('assignment')->insert([
							'uuid' => $uuid,
							'class_id' => $a[1],
							'subject_id' => $s->id,
						]);
					}
				}
			}
			$emp->idno = $user->cn;
			$emp->id = $user->employeeNumber;
			$emp->account = $account;
			$emp->sn = $user->sn;
			$emp->gn = $user->givenName;
			$emp->realname = $user->displayName;
			$emp->birthdate = $birth;
			$emp->gender = $user->gender;
			if (!empty($user->mobile)) {
				$emp->mobile = $user->mobile;
			}
			if (!empty($user->telephoneNumber)) {
				$emp->telephone = $user->telephoneNumber;
			}
			if (!empty($user->homePhone)) {
				$emp->telephone = $user->homePhone;
			}
			if (!empty($user->registeredAddress)) {
				$emp->address = $user->registeredAddress;
			}
			if (!empty($user->homePostalAddress)) {
				$emp->address = $user->homePostalAddress;
			}
			if (!empty($user->mail)) {
				$emp->email = preg_replace('/\s(?=)/', '', $user->mail);
			}
			if (!empty($user->wWWHomePage)) {
				$emp->www = $user->wWWHomePage;
			}
			if (!empty($user->tpCharacter)) {
				if (is_array($user->tpCharacter)) {
					$emp->character = implode(',', $user->tpCharacter);
				} else {
					$emp->character = $user->tpCharacter;
				}
			}
			$emp->save();
			return true;
		}
		return false;
	}

	function user_type($uuid) {
		$user = $this->api('one_user', ['uuid' => $uuid]);
		if ($user) {
			if ($user->employeeType == '學生') {
				return 'Student';
			} else {
				return 'Teacher';
			}
		}
	}

	function sync_units()
	{
		$fetch = Unit::first();
		if ($fetch) {
			$expire = new Carbon($fetch->updated_at);
    		if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) return false;
		}
		$ous = $this->api('all_units');
		if ($ous) {
			foreach ($ous as $o) {
				$unit = Unit::firstOrNew(['id' => $o->ou]);
				$unit->name = $o->description;
				$unit->save();
			}
		}
	}

	function sync_roles()
	{
		$fetch = Role::first();
		if ($fetch) {
			$expire = new Carbon($fetch->updated_at);
    		if (Carbon::today() > $expire->addDays(config('services.tpedu.expired_days'))) {
				DB::table('roles')->delete();
			}
		}
	}

	function sync_subjects()
	{
		$fetch = Subject::first();
		if ($fetch) {
			$expire = new Carbon($fetch->updated_at);
    		if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) return false;
		}
		$subjects = $this->api('all_subjects');
		if ($subjects) {
			foreach ($subjects as $s) {
				$subj = Subject::firstOrNew(['name' => $s->description]);
				$subj->save();
			}
		}
	}

	function sync_classes()
	{
		$fetch = Classroom::first();
		if ($fetch) {
			$expire = new Carbon($fetch->updated_at);
    		if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) return false;
		}
		$classes = $this->api('all_classes');
		if ($classes) {
			foreach ($classes as $c) {
				$cls = Classroom::firstOrNew(['id' => $c->ou]);
				$cls->grade_id = $c->grade;
				$cls->name = $c->description;
				$tutors = [];
				foreach ($c->tutor as $t) {
					if ($t) $tutors[] = $t;
				}
				if (!empty($tutors)) {
					$cls->tutor = $tutors;
				}
				$cls->save();
			}
		}
	}

}