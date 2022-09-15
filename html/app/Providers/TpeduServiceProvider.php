<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\STR;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Grade;
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
				config('services.tpedu.endpoint.login') . '?' .
				'client_id' . '=' . config('services.tpedu.app') . '&' .
				'redirect_uri' . '=' . config('services.tpedu.callback') . '&' .
				'response_type=code&scope=user';
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
				return $user->uuid;
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

	public function fetch_user($uuid, $only = false, $pwd = false)
	{
		if ($only) {
			$temp = Student::find($uuid);
			if ($temp) {
				if (!$temp->expired()) return false;
			} else {
				$temp = Teacher::find($uuid);
				if ($temp) {
					$expire = new Carbon($temp->updated_at);
					if (!$temp->expired()) return false;
				}
			}	
		}
		$o = config('services.tpedu.school');
		$user = $this->api('one_user', ['uuid' => $uuid]);
		if ($user) {
			if (is_array($user->uid)) {
				foreach ($user->uid as $u) {
					if (!strpos($u, '@') && !$this->is_phone($u)) {
						$account = $u;
					}
				}
			} else {
				$account = $user->uid;
			}
			$sys_user = User::where('uuid', $uuid)->first();
			if ($pwd && $sys_user) {
				$sys_user->forceFill([
					'password' => Hash::make(substr($user->cn, -6))
				])->setRememberToken(Str::random(60));
				$sys_user->save();;
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
				if (isset($user->ou) && isset($user->title)) {
					$keywords = explode(',', config('services.tpedu.base_unit'));
					if (is_array($user->ou)) {
						foreach ($user->ou as $ou_pair) {
							$a = explode(',', $ou_pair);
							if ($a[0] == $o) {
								$dept = Unit::where('unit_no', $a[1])->first();
								$ckf = false;
								foreach ($keywords as $k) {
									if (!(mb_strpos($dept->name, $k) === false)) {
										$ckf = true;
									}
								}
								if (!$ckf || empty($m_dept_id)) {
									$m_dept_id = $dept->id;
									$m_dept_name = $dept->name;
								}
							}
						}
					} else {
						$a = explode(',', $user->ou);
						if ($a[0] == $o) {
							$dept = Unit::where('unit_no', $a[1])->first();
							$m_dept_id = $dept->id;
							$m_dept_name = $dept->name;	
						}
					}
					$emp->unit_id = $m_dept_id;
					$emp->unit_name = $m_dept_name;
					if (is_array($user->titleName->{$o})) {
						foreach ($user->titleName->{$o} as $ro) {
							$a = explode(',', $ro->key);
							if ($a[0] == $o) {
								$role_name = $ro->name; 
								$ckf = false;
								foreach ($keywords as $k) {
									if (!(mb_strpos($role_name, $k) === false)) {
										$ckf = true;
									}
								}
								$dept = Unit::where('unit_no', $a[1])->first();
								$role = Role::where('role_no', $a[2])->where('unit_id', $dept->id)->first();
								if (!$role) {
									$role = Role::create([
										'role_no' => $a[2],
										'unit_id' => $dept->id,
										'name' => $role_name,
									]);	
								}
								if (!$ckf || empty($m_role_id)) {
									$m_role_id = $role->id;
									$m_role_name = $role_name;
								}
								DB::table('job_title')->insert([
									'uuid' => $uuid,
									'unit_id' => $dept->id,
									'role_id' => $role->id,
								]);	
							}
						}
					} else {
						$ro = $user->titleName->{$o};
						$a = explode(',', $ro->key);
						if ($a[0] == $o) {
							$role_name = $ro->name;
							$dept = Unit::where('unit_no', $a[1])->first();
							$role = Role::where('role_no', $a[2])->where('unit_id', $dept->id)->first();
							if (!$role) {
								$role = Role::create([
									'role_no' => $a[2],
									'unit_id' => $dept->id,
									'name' => $role_name,
								]);
							}
							$m_role_id = $role->id;
							$m_role_name = $role_name;
							DB::table('job_title')->insert([
								'uuid' => $uuid,
								'unit_id' => $dept->id,
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
				DB::table('assignment')->where('uuid', $uuid)->delete();
				if (isset($user->tpTeachClass)) {
					foreach ($user->tpTeachClass as $assign_pair) {
						$a = explode(',', $assign_pair);
						$s = Subject::where('name', mb_substr($a[2], 4))->first();
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

	function sync_units($only = false, $sync = false)
	{
		$detail_log = [];
		$fetch = Unit::first();
		if ($fetch) {
			if (!$sync) {
				$detail_log[] = '快取資料庫中已經有行政單位資料，跳過不處理！';
				return $detail_log;
			}
			if ($only) {
				$expire = new Carbon($fetch->updated_at);
				if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) {
					$detail_log[] = '快取資料庫中已經有行政單位資料，且資料尚未過期，跳過不處理！';
					return $detail_log;
				}
			}
		}
		$ous = $this->api('all_units');
		if ($ous) {
			foreach ($ous as $o) {
				$unit = Unit::where('unit_no', $o->ou)->first();
				if (!$unit) {
					$unit = new Unit;
				}
				$unit->unit_no = $o->ou;
				$unit->name = $o->description;
				$unit->save();
				$detail_log[] = '行政單位'.$o->ou.' '.$o->description.'同步完成！';
			}
		}
		return $detail_log;
	}

	function sync_roles($only = false, $sync = false)
	{
		$detail_log = [];
		if (!$only && $sync) {
			DB::table('roles')->truncate();
			$detail_log[] = '快取資料庫中已經有職稱資料，職稱資料必須在同步教師資料時才能同步處理，已經將職稱資料清空！';
		}
		return $detail_log;
	}

	function sync_subjects($only = false, $sync = false)
	{
		$detail_log = [];
		$fetch = Subject::first();
		if ($fetch) {
			if (!$sync) {
				$detail_log[] = '快取資料庫中已經有科目資料，跳過不處理！';
				return $detail_log;
			}
			if ($only) {
				$expire = new Carbon($fetch->updated_at);
				if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) {
					$detail_log[] = '快取資料庫中已經有科目資料，且資料尚未過期，跳過不處理！';
					return $detail_log;
				}
			}
		}
		$subjects = $this->api('all_subjects');
		if ($subjects) {
			foreach ($subjects as $s) {
				$subj = Subject::firstOrNew(['name' => $s->description]);
				$subj->save();
				$detail_log[] = '科目'.$s->description.'同步完成！';
			}
		}
		return $detail_log;
	}

	function sync_classes($only = false, $sync = false)
	{
		$detail_log = [];
		$fetch = Classroom::first();
		if ($fetch) {
			if (!$sync) {
				$detail_log[] = '快取資料庫中已經有班級資料，跳過不處理！';
				return $detail_log;
			}
			if ($only) {
				$expire = new Carbon($fetch->updated_at);
				if (Carbon::today() < $expire->addDays(config('services.tpedu.expired_days'))) {
					$detail_log[] = '快取資料庫中已經有班級資料，且資料尚未過期，跳過不處理！';
					return $detail_log;
				}
			}
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
				$detail_log[] = ' 班級'.$c->description.'同步完成！';
			}
		}
		return $detail_log;
	}

	function sync_teachers($only = false, $sync = false, $password = false, $remove = true)
	{
		$detail_log = [];
		$fetch = Teacher::first();
		if ($fetch && !$sync) {
			$detail_log[] = '快取資料庫中已經有教師資料，跳過不處理！';
			return $detail_log;
		}
    	$uuids = $this->api('all_teachers');
    	if ($uuids && is_array($uuids)) {
        	foreach ($uuids as $uuid) {
            	$this->fetch_user($uuid, $only, $password);
				$t = Teacher::find($uuid);
				$detail_log[] = '教師'.$t->idno.' '.$t->realname.'已同步完成！';
        	}
    	}
		if ($remove) {
			$leaves = Teacher::whereNotIN('uuid', $uuids)->get();
			foreach ($leaves as $l) {
				$detail_log[] = '離職教師'.$l->idno.' '.$l->realname.'已刪除！';
				User::destroy($l->uuid);
				$l->delete();
			}	
		}
		return $detail_log;
	}

	function sync_students($only = false, $password = false, $remove = true)
	{
		$detail_log[] = '開始同步全校學生資料！';
		$classes = Classroom::all();
        foreach ($classes as $cls) {
			$uuids = $this->api('students_of_class', ['class' => $cls->id]);
			if ($uuids && is_array($uuids)) {
				foreach ($uuids as $uuid) {
					$this->fetch_user($uuid, $only, $password);
					$s = Student::find($uuid);
					$detail_log[] = '學生'.$s->idno.' '.$s->realname.'已同步完成！';
				}
			}
			if ($remove) {
				$leaves = Student::where('class_id', $cls->id)->whereNotIN('uuid', $uuids)->get();
				foreach ($leaves as $l) {
					$detail_log[] = '轉學或畢業學生'.$l->idno.' '.$l->realname.'已刪除！';
					User::destroy($l->uuid);
					$l->delete();
				}	
			}
		}
		return $detail_log;
	}

	function sync_students_for_grade($grade, $only = false, $password = false, $remove = true)
	{
		$detail_log[] = '開始同步'.$grade.'年級學生資料！';
		$classes = Grade::find($grade)->classrooms;
        foreach ($classes as $cls) {
			$uuids = $this->api('students_of_class', ['class' => $cls->id]);
			if ($uuids && is_array($uuids)) {
				foreach ($uuids as $uuid) {
					$this->fetch_user($uuid, $only, $password);
					$s = Student::find($uuid);
					$detail_log[] = '學生'.$s->idno.' '.$s->realname.'已同步完成！';
				}
			}
			if ($remove) {
				$leaves = Student::where('class_id', $cls->id)->whereNotIN('uuid', $uuids)->get();
				foreach ($leaves as $l) {
					$detail_log[] = '轉學或畢業學生'.$l->idno.' '.$l->realname.'已刪除！';
					User::destroy($l->uuid);
					$l->delete();
				}	
			}
		}
		return $detail_log;
	}

	function sync_students_for_class($class_id, $only = false, $password = false, $remove = true)
	{
		$detail_log[] = '開始同步'.$class_id.'班級學生資料！';
		$uuids = $this->api('students_of_class', ['class' => $class_id]);
		if ($uuids && is_array($uuids)) {
			foreach ($uuids as $uuid) {
				$this->fetch_user($uuid, $only, $password);
				$s = Student::find($uuid);
				$detail_log[] = '學生'.$s->idno.' '.$s->realname.'已同步完成！';
			}
		}
		if ($remove) {
			$leaves = Student::where('class_id', $class_id)->whereNotIN('uuid', $uuids)->get();
			foreach ($leaves as $l) {
				$detail_log[] = '轉學或畢業學生'.$l->idno.' '.$l->realname.'已刪除！';
				User::destroy($l->uuid);
				$l->delete();
			}	
		}
	}

}