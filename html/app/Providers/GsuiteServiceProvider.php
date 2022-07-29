<?php

namespace App\Providers;

use Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gsuite;
use App\Models\Student;
use App\Models\Teacher;

class GsuiteServiceProvider extends ServiceProvider
{

	private static $directory = null;

    public function __construct()
    {
        if (is_null(self::$directory)) {
			$this->init();
		}
    }

	public function init()
	{
		$path = config('services.gsuite.auth_config');
		$user_to_impersonate = config('services.gsuite.admin');
		$scopes = [
			\Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT,
			\Google_Service_Directory::ADMIN_DIRECTORY_USER,
			\Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
			\Google_Service_Directory::ADMIN_DIRECTORY_GROUP_MEMBER,
		];

		$client = new \Google_Client();
		$client->setAuthConfig($path);
		$client->setApplicationName('School Web Site');
		$client->setScopes($scopes);
		$client->setSubject($user_to_impersonate);
		try {
			self::$directory = new \Google_Service_Directory($client);
		} catch (\Google_Service_Exception $e) {
			Log::error('google directory:' . $e->getMessage());
		}
	}

	public function list_orgunits()
	{
		try {
			$result = self::$directory->orgunits->listOrgunits('my_customer');	
			return $result->getOrganizationUnits();
		} catch (\Google_Service_Exception $e) {
			Log::notice('google listOrgUnits:' . $e->getMessage());
			return false;
		}
	}
	
	public function get_orgunit($orgPath)
	{
		try {
			return self::$directory->orgunits->get('my_customer', $orgPath);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google getOrgUnit($orgPath):" . $e->getMessage());
			return false;
		}
	}
	
	public function create_orgunit($orgPath, $orgName, $orgDescription)
	{
		$org_unit = new \Google_Service_Directory_OrgUnit();
		$org_unit->setName($orgName);
		$org_unit->setDescription($orgDescription);
		$org_unit->setParentOrgUnitPath($orgPath);
		try {
			return self::$directory->orgunits->insert('my_customer', $org_unit);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google createOrgUnit($orgPath,$orgName,$orgDescription):" . $e->getMessage());	
			return false;
		}
	}
	
	public function update_orgunit($orgPath, $orgName)
	{
		$org_unit = new \Google_Service_Directory_OrgUnit();
		$org_unit->setDescription($orgName);
		try {
			return self::$directory->orgunits->update('my_customer', $orgPath, $org_unit);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google updateOrgUnit($orgPath,$orgName):" . $e->getMessage());
			return false;
		}
	}
	
	public function delete_orgunit($orgPath)
	{
		try {
			return self::$directory->orgunits->delete('my_customer', $orgPath);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google deleteOrgUnit($orgPath):" . $e->getMessage());
			return false;
		}
	}
	
	public function find_users($filter)
	{
		try {
			$result = self::$directory->users->listUsers(['domain' => config('services.gsuite.domain'), 'query' => $filter]);
			return $result->getUsers();
		} catch (\Google_Service_Exception $e) {
			Log::notice("google findUsers($filter):" . $e->getMessage());
			return false;
		}
	}
	
	public function get_user($userKey)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->users->get($userKey);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google getUser($userKey):" . $e->getMessage());
			return false;
		}
	}
	
	public function create_user(\Google_Service_Directory_User $userObj)
	{
		try {
			return self::$directory->users->insert($userObj);
		} catch (\Google_Service_Exception $e) {
			Log::notice('google createUser('.var_export($userObj, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function update_user($userKey, \Google_Service_Directory_User $userObj)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->users->update($userKey, $userObj);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google updateUser($userKey,".var_export($userObj, true).'):' . $e->getMessage());
			return false;
		}
	}
	
	public function delete_user($userKey)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->users->delete($userKey);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google deleteUser($userKey):" . $e->getMessage());
			return false;
		}
	}
	
	public function sync_user(Model $t, $userKey, $recover = false)
	{
		if ( !($t instanceof Student) || !($t instanceof Teacher) ) return false;
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		$config = \Drupal::config('gsync.settings');
		if ($user = get_user($userKey)) {
			$create = false;
			$old_key = $user->getPrimaryEmail();
			if ($old_key != $userKey) {
				$user->setPrimaryEmail($userKey);
			}
			if ($recover) {
				$user->setHashFunction('SHA-1');
				$user->setPassword(sha1(substr($t->idno, -6)));
			}
		} else {
			$create = true;
			$user = new \Google_Service_Directory_User();
			$user->setChangePasswordAtNextLogin(false);
			$user->setAgreedToTerms(true);
			$user->setPrimaryEmail($userKey);
			$user->setHashFunction('SHA-1');
			$user->setPassword(sha1(substr($t->idno, -6)));
		}
		$gmails = $t->gmails(); //Gsuite::where('uuid', $t->uuid)->get();
		if (empty($gmails)) {
			Gsuite::create([
				'uuid' => $t->uuid,
				'userKey' => $userKey,
				'primary' => true,
			]);		
		} else {
			$found = false;
			foreach ($gmails as $gmail) {
				if ($gmail->userKey == $userKey) {
					$found = true;
					if (!$gmail->primary) {
						$gmail->primary = true;
						$gmail->save();
					}
				} elseif ($gmail->primary) {
					$gmail->primary = false;
					$gmail->save();
				}
			}
			if (! $found) {
				Gsuite::create([
					'uuid' => $t->uuid,
					'userKey' => $userKey,
					'primary' => true,
				]);	
			}
		}
		$sysid = new \Google_Service_Directory_UserExternalId();
		$sysid->setType('organization');
		$sysid->setValue($t->uuid);
		$user->setExternalIds([$sysid]);
		$names = new \Google_Service_Directory_UserName();
		if ($t->sn && $t->gn) {
			$names->setFamilyName($t->sn);
			$names->setGivenName($t->gn);
		} else {
			$myname = $this->guess_name($t->realname);
			$names->setFamilyName($myname[0]);
			$names->setGivenName($myname[1]);
		}
		$names->setFullName($t->realname);
		$user->setName($names);
		if (!empty($t->email) && $t->email != $user->getPrimaryEmail()) {
			$user->setRecoveryEmail($t->email);
		}
		$orgs = [];
		if ($t instanceof Student) {
			$neworg = new \Google_Service_Directory_UserOrganization();
			$neworg->setType('school');
			$neworg->setDepartment('學生');
			$neworg->setTitle($t->classroom()->name . $t->seat . '號');
			$neworg->setPrimary(true);
			$orgs[] = $neworg;
			if (config('services.gsuite.student_orgunit')) {
				$user->setOrgUnitPath(config('services.gsuite.student_orgunit'));
			}
		} elseif ($t instanceof Teacher) {
			$jobs = $t->roles();
			foreach ($jobs as $job) {
				$neworg = new \Google_Service_Directory_UserOrganization();
				$neworg->setType('school');
				$neworg->setDepartment($job->unit()->name);
				$neworg->setTitle($job->name);
				if ($job->id == $t->role_id) {
					$neworg->setPrimary(true);
				}
				$orgs[] = $neworg;
			}
			$user->setIsAdmin(true);
			if (config('services.gsuite.teacher_orgunit')) {
				$user->setOrgUnitPath(config('services.gsuite.teacher_orgunit'));
			}
		}
		if (!empty($orgs)) {
			$user->setOrganizations($orgs);
		}
		if (!empty($t->mobile)) {
			$phones = $user->getPhones();
			$phone = new \Google_Service_Directory_UserPhone();
			$phone->setPrimary(true);
			$phone->setType('mobile');
			$phone->setValue($t->mobile);
			if (is_array($phones)) {
				if (!in_array($phone, $phones)) {
					$phones = array_unshift($phones, $phone);
				}
			}
			$user->setPhones($phones);
		}
		if (!empty($t->telephone)) {
			$phones = $user->getPhones();
			$phone = new \Google_Service_Directory_UserPhone();
			$phone->setPrimary(false);
			$phone->setValue($t->telephone);
			if (is_array($phones)) {
				if (!in_array($phone, $phones)) {
					$phones = array_unshift($phones, $phone);
				}
			}
			$user->setPhones($phones);
		}
	
		$gender = new \Google_Service_Directory_UserGender();
		if (!empty($t->gender)) {
			switch ($t->gender) {
				case 0:
					$gender->setType('unknow');
					break;
				case 1:
					$gender->setType('male');
					break;
				case 2:
					$gender->setType('female');
					break;
				case 9:
					$gender->setType('other');
					break;
			}
		}
		$user->setGender($gender);
		if ($create) {
			return $this->create_user($user);
		} else {
			return $this->update_user($user_key, $user);
		}
	}
	
	public function create_alias($userKey, $alias)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		$email_alias = new \Google_Service_Directory_Alias();
		$email_alias->setAlias($alias);
		try {
			return self::$directory->users_aliases->insert($userKey, $email_alias);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google createUserAlias($userKey, $alias):" . $e->getMessage());
			return false;
		}
	}
	
	public function list_aliases($userKey)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->users_aliases->listUsersAliases($userKey);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google listUserAliases($userKey):" . $e->getMessage());
			return false;
		}
	}
	
	public function remove_alias($userKey, $alias)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->users_aliases->delete($userKey, $alias);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google removeUserAlias($userKey,$alias):" . $e->getMessage());
			return false;
		}
	}
	
	public function all_groups()
	{
		try {
			return self::$directory->groups->listGroups(['domain' => config('services.gsuite.domain')])->getGroups();
		} catch (\Google_Service_Exception $e) {
			Log::notice('google listGroups for All:' . $e->getMessage());
			return false;
		}
	}
	
	public function list_groups($userKey)
	{
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->groups->listGroups(['domain' => config('services.gsuite.domain'), 'userKey' => $userKey])->getGroups();
		} catch (\Google_Service_Exception $e) {
			Log::notice("google listGroups for user $userKey:" . $e->getMessage());
			return false;
		}
	}
	
	public function create_group($groupId, $groupName)
	{
		if (!strpos($groupId, '@')) {
			$groupId .= '@' . config('services.gsuite.domain');
		}
		$group = new Google_Service_Directory_Group();
		$group->setEmail($groupId);
		$group->setDescription($groupName);
		$group->setName($groupName);
		try {
			return self::$directory->groups->insert($group);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google createGroup($groupId,$groupName):" . $e->getMessage());
			return false;
		}
	}
	
	public function list_members($groupId)
	{
		if (!strpos($groupId, '@')) {
			$groupId .= '@' . config('services.gsuite.domain');
		}
		try {
			return $directory->members->listMembers($groupId)->getMembers();
		} catch (\Google_Service_Exception $e) {
			\Drupal::logger('google')->debug("gs_listMembers($groupId):".$e->getMessage());
	
			return false;
		}
	}
	
	public function add_member($groupId, $userKey)
	{
		if (!strpos($groupId, '@')) {
			$groupId .= '@' . config('services.gsuite.domain');
		}
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		$memberObj = new \Google_Service_Directory_Member();
		$memberObj->setEmail($userKey);
		$memberObj->setRole('MEMBER');
		$memberObj->setType('USER');
		$memberObj->setStatus('ACTIVE');
		try {
			return self::$directory->members->insert($groupId, $memberObj);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google addMember($groupId,$userKey):" . $e->getMessage());
			return false;
		}
	}
	
	public function remove_member($groupId, $userKey)
	{
		if (!strpos($groupId, '@')) {
			$groupId .= '@' . config('services.gsuite.domain');
		}
		if (!strpos($userKey, '@')) {
			$userKey .= '@' . config('services.gsuite.domain');
		}
		try {
			return self::$directory->members->delete($groupId, $userKey);
		} catch (\Google_Service_Exception $e) {
			Log::notice("google removeMember($groupId,$userKey):" . $e->getMessage());
			return false;
		}
	}
	
	private function guess_name($myname)
	{
		$len = mb_strlen($myname, 'UTF-8');
		if ($len > 3) {
			return [mb_substr($myname, 0, 2, 'UTF-8'), mb_substr($myname, 2, null, 'UTF-8')];
		} else {
			return [mb_substr($myname, 0, 1, 'UTF-8'), mb_substr($myname, 1, null, 'UTF-8')];
		}
	}
}