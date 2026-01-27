<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Google_Service_Directory;
use Google_Service_Exception;
use Google_Service_Directory_OrgUnit;
use Google_Service_Directory_User;
use Google_Service_Directory_UserExternalId;
use Google_Service_Directory_UserName;
use Google_Service_Directory_UserOrganization;
use Google_Service_Directory_UserPhone;
use Google_Service_Directory_UserGender;
use Google_Service_Directory_Alias;
use Google_Service_Directory_Group;
use Google_Service_Directory_Member;
use Google_Client;
use App\Models\Unit;
use App\Models\Gsuite;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Student;

class GsuiteServiceProvider extends ServiceProvider
{

    private $directory = null;

    public function __construct()
    {
        if (is_null($this->directory)) {
            $this->init();
        }
    }

    public function init()
    {
        $path = config('services.gsuite.auth_config');
        $user_to_impersonate = config('services.gsuite.admin');
        $scopes = [
            Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT,
            Google_Service_Directory::ADMIN_DIRECTORY_USER,
            Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
            Google_Service_Directory::ADMIN_DIRECTORY_GROUP_MEMBER,
        ];

        $client = new Google_Client();
        $client->setAuthConfig($path);
        $client->setApplicationName('School Web Site');
        $client->setScopes($scopes);
        $client->setSubject($user_to_impersonate);
        try {
            $this->directory = new Google_Service_Directory($client);
        } catch (Google_Service_Exception $e) {
            Log::error('google directory:' . $e->getMessage());
        }
    }

    public function list_orgunits()
    {
        try {
            $result = $this->directory->orgunits->listOrgunits('my_customer');
            return $result->getOrganizationUnits();
        } catch (Google_Service_Exception $e) {
            Log::notice('google listOrgUnits:' . $e->getMessage());
            return false;
        }
    }

    public function get_orgunit($orgPath)
    {
        try {
            return $this->directory->orgunits->get('my_customer', $orgPath);
        } catch (Google_Service_Exception $e) {
            Log::notice("google getOrgUnit($orgPath):" . $e->getMessage());
            return false;
        }
    }

    public function create_orgunit($orgPath, $orgName, $orgDescription)
    {
        $org_unit = new Google_Service_Directory_OrgUnit();
        $org_unit->setName($orgName);
        $org_unit->setDescription($orgDescription);
        $org_unit->setParentOrgUnitPath($orgPath);
        try {
            return $this->directory->orgunits->insert('my_customer', $org_unit);
        } catch (Google_Service_Exception $e) {
            Log::notice("google createOrgUnit($orgPath,$orgName,$orgDescription):" . $e->getMessage());
            return false;
        }
    }

    public function update_orgunit($orgPath, $orgName)
    {
        $org_unit = new Google_Service_Directory_OrgUnit();
        $org_unit->setDescription($orgName);
        try {
            return $this->directory->orgunits->update('my_customer', $orgPath, $org_unit);
        } catch (Google_Service_Exception $e) {
            Log::notice("google updateOrgUnit($orgPath,$orgName):" . $e->getMessage());
            return false;
        }
    }

    public function delete_orgunit($orgPath)
    {
        try {
            return $this->directory->orgunits->delete('my_customer', $orgPath);
        } catch (Google_Service_Exception $e) {
            Log::notice("google deleteOrgUnit($orgPath):" . $e->getMessage());
            return false;
        }
    }

    public function all_users($user_type)
    {
        if ($user_type == 'student') {
            $org_unit = config('services.gsuite.student_orgunit');
        } elseif ($user_type == 'teacher') {
            $org_unit = config('services.gsuite.teacher_orgunit');
        }
        $users = [];
        $opt = [
            'domain' => config('services.gsuite.domain'),
            'query' => "orgUnitPath:$org_unit",
        ];
        $page_token = null;
        try {
            do {
                if ($page_token)
                    $opt['pageToken'] = $page_token;
                $result = $this->directory->users->listUsers($opt);
                $page_token = $result->getNextPageToken();
                $users = array_merge($users, $result->getUsers());
            } while ($page_token);
            return $users;
        } catch (Google_Service_Exception $e) {
            Log::notice("google allUsers($user_type):" . $e->getMessage());
            return false;
        }
    }

    public function find_users($filter)
    {
        $users = [];
        $opt = [
            'domain' => config('services.gsuite.domain'),
            'query' => $filter,
        ];
        $page_token = null;
        try {
            do {
                if ($page_token)
                    $opt['pageToken'] = $page_token;
                $result = $this->directory->users->listUsers($opt);
                $page_token = $result->getNextPageToken();
                if ($result->getUsers()) {
                    $users = array_merge($users, $result->getUsers());
                }
            } while ($page_token);
            return $users;
        } catch (Google_Service_Exception $e) {
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
            return $this->directory->users->get($userKey);
        } catch (Google_Service_Exception $e) {
            Log::notice("google getUser($userKey):" . $e->getMessage());
            return false;
        }
    }

    public function create_user(Google_Service_Directory_User $userObj)
    {
        try {
            return $this->directory->users->insert($userObj);
        } catch (Google_Service_Exception $e) {
            Log::notice('google createUser(' . var_export($userObj, true) . '):' . $e->getMessage());
            return false;
        }
    }

    public function update_user($userKey, Google_Service_Directory_User $userObj)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->users->update($userKey, $userObj);
        } catch (Google_Service_Exception $e) {
            Log::notice("google updateUser($userKey," . var_export($userObj, true) . '):' . $e->getMessage());
            return false;
        }
    }

    public function suspend_user($userKey)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        try {
            $user = $this->directory->users->get($userKey);
            $user->setSuspended(true);
            return $this->directory->users->update($userKey, $user);
        } catch (Google_Service_Exception $e) {
            Log::notice("google deleteUser($userKey):" . $e->getMessage());
            return false;
        }
    }

    public function delete_user($userKey)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->users->delete($userKey);
        } catch (Google_Service_Exception $e) {
            Log::notice("google deleteUser($userKey):" . $e->getMessage());
            return false;
        }
    }

    public function reset_password($userKey, $passwd)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        $user = $this->get_user($userKey);
        if ($user) {
            $user->setHashFunction('SHA-1');
            $user->setPassword(sha1($passwd));
            return $this->update_user($userKey, $user);
        }
        return false;
    }

    public function sync_user(Model $t, $userKey, $user = null, $recover = false)
    {
        if ($t instanceof Student) {
            $user_type = 'Student';
        } elseif ($t instanceof Teacher) {
            $user_type = 'Teacher';
        } else {
            return false;
        }
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        if ($user) {
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
            $user = new Google_Service_Directory_User();
            $user->setChangePasswordAtNextLogin(false);
            $user->setAgreedToTerms(true);
            $user->setPrimaryEmail($userKey);
            $user->setHashFunction('SHA-1');
            $user->setPassword(sha1(substr($t->idno, -6)));
        }
        $gmails = $t->gmails;
        if (empty($gmails)) {
            Gsuite::create([
                'owner_id' => $t->uuid,
                'owner_type' => 'App\\Models\\' . $user_type,
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
            if (!$found) {
                Gsuite::create([
                    'owner_id' => $t->uuid,
                    'owner_type' => 'App\\Models\\' . $user_type,
                    'userKey' => $userKey,
                    'primary' => true,
                ]);
            }
        }
        $sysid = new Google_Service_Directory_UserExternalId();
        $sysid->setType('organization');
        $sysid->setValue($t->uuid);
        $user->setExternalIds([$sysid]);
        $names = new Google_Service_Directory_UserName();
        if ($user_type == 'Student') {
            $seat = ($t->seat < 10) ? '0' . $t->seat : $t->seat;
            $names->setFamilyName($seat . $t->sn);
            $names->setGivenName($t->gn);
            $names->setFullName($t->seat . $t->realname);
        } elseif ($user_type == 'Teacher') {
            $names->setFamilyName($t->sn);
            $names->setGivenName($t->gn);
            $names->setFullName($t->realname);
        }
        $user->setName($names);
        if (!empty($t->email) && $t->email != $user->getPrimaryEmail()) {
            $user->setRecoveryEmail($t->email);
        }
        $orgs = [];
        if ($user_type == 'Student') {
            $neworg = new Google_Service_Directory_UserOrganization();
            $neworg->setType('school');
            $neworg->setDescription('Student');
            $neworg->setDepartment(substr($t->id, 0, 3));
            $neworg->setTitle($t->classroom->name . $t->seat . '號');
            $neworg->setPrimary(true);
            $orgs[] = $neworg;
            if (config('services.gsuite.student_orgunit')) {
                $user->setOrgUnitPath(config('services.gsuite.student_orgunit'));
            }
        } elseif ($user_type == 'Teacher') {
            $jobs = $t->roles;
            foreach ($jobs as $job) {
                $neworg = new Google_Service_Directory_UserOrganization();
                $neworg->setType('school');
                $neworg->setDescription('Teacher');
                $neworg->setDepartment($job->unit->name);
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
            $phone = new Google_Service_Directory_UserPhone();
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
            $phone = new Google_Service_Directory_UserPhone();
            $phone->setPrimary(false);
            $phone->setValue($t->telephone);
            if (is_array($phones)) {
                if (!in_array($phone, $phones)) {
                    $phones = array_unshift($phones, $phone);
                }
            }
            $user->setPhones($phones);
        }

        $gender = new Google_Service_Directory_UserGender();
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
            return $this->update_user($userKey, $user);
        }
    }

    public function create_alias($userKey, $alias)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        if (!strpos($alias, '@')) {
            $alias .= '@' . config('services.gsuite.domain');
        }
        $email_alias = new Google_Service_Directory_Alias();
        $email_alias->setAlias($alias);
        try {
            return $this->directory->users_aliases->insert($userKey, $email_alias);
        } catch (Google_Service_Exception $e) {
            Log::notice("google createUserAlias($userKey, $alias):" . $e->getMessage());
            return false;
        }
    }

    public function has_alias($userKey, $alias)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        if (!strpos($alias, '@')) {
            $alias .= '@' . config('services.gsuite.domain');
        }
        $found = false;
        $aliases = $this->list_aliases($userKey);
        if (!$aliases)
            return false;
        foreach ($aliases as $a) {
            if ($a['alias'] == $alias) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    public function list_aliases($userKey)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->users_aliases->listUsersAliases($userKey)->getAliases();
        } catch (Google_Service_Exception $e) {
            Log::notice("google listUserAliases($userKey):" . $e->getMessage());
            return false;
        }
    }

    public function remove_alias($userKey, $alias)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        if (!strpos($alias, '@')) {
            $alias .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->users_aliases->delete($userKey, $alias);
        } catch (Google_Service_Exception $e) {
            Log::notice("google removeUserAlias($userKey,$alias):" . $e->getMessage());
            return false;
        }
    }

    public function all_groups()
    {
        try {
            return $this->directory->groups->listGroups(['domain' => config('services.gsuite.domain')])->getGroups();
        } catch (Google_Service_Exception $e) {
            Log::notice('google listGroups for All:' . $e->getMessage());
            return false;
        }
    }

    public function get_group($groupKey)
    {
        if (!strpos($groupKey, '@')) {
            $groupKey .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->groups->get($groupKey);
        } catch (Google_Service_Exception $e) {
            Log::notice("google getGroup($groupKey):" . $e->getMessage());
            return false;
        }
    }

    public function list_groups($userKey)
    {
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        try {
            return $this->directory->groups->listGroups(['domain' => config('services.gsuite.domain'), 'userKey' => $userKey])->getGroups();
        } catch (Google_Service_Exception $e) {
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
            return $this->directory->groups->insert($group);
        } catch (Google_Service_Exception $e) {
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
            return $this->directory->members->listMembers($groupId)->getMembers();
        } catch (Google_Service_Exception $e) {
            Log::debug("gs_listMembers($groupId):" . $e->getMessage());

            return false;
        }
    }

    public function add_member($groupId, $userKey, $role = null)
    {
        if (!strpos($groupId, '@')) {
            $groupId .= '@' . config('services.gsuite.domain');
        }
        if (!strpos($userKey, '@')) {
            $userKey .= '@' . config('services.gsuite.domain');
        }
        if (!$role)
            $role = 'MEMBER';
        $memberObj = new Google_Service_Directory_Member();
        $memberObj->setEmail($userKey);
        $memberObj->setRole($role);
        $memberObj->setType('USER');
        $memberObj->setStatus('ACTIVE');
        try {
            return $this->directory->members->insert($groupId, $memberObj);
        } catch (Google_Service_Exception $e) {
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
            return $this->directory->members->delete($groupId, $userKey);
        } catch (Google_Service_Exception $e) {
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

    public function sync_teachers($password_sync = false)
    {
        $detail_log = [];
        $domain = config('services.gsuite.domain');
        $all_groups = $this->all_groups();
        if (!$all_groups) {
            $all_groups = [];
        }
        $teachers = Teacher::all();
        if (!empty($teachers)) {
            foreach ($teachers as $t) {
                $groups = [];
                if (!empty($t->email)) {
                    $user_key = $t->email;
                    list($account, $path) = explode('@', $user_key);
                    if ($domain != $path) {
                        $user_key = $t->account . '@' . $domain;
                    }
                } else {
                    $user_key = $t->account . '@' . $domain;
                }
                $detail_log[] = "正在處理 $t->role_name $t->realname ($user_key)......";
                $user = $this->get_user($user_key);
                if (!$user) {
                    $result = $this->find_users('externalId=' . $t->id);
                    if ($result) {
                        $user = $result[0];
                        $user_key = $user->getPrimaryEmail();
                    } else {
                        $result = $this->find_users('externalId=' . $t->uuid);
                        if ($result) {
                            $user = $result[0];
                            $user_key = $user->getPrimaryEmail();
                        }
                    }
                }
                if ($user) {
                    $detail_log[] = '在 G Suite 中找到這位使用者';
                    $data = $this->list_groups($t->account);
                    if ($data) {
                        foreach ($data as $g) {
                            $gn = strtolower($g->getEmail());
                            $desc = $g->getDescription();
                            if ((substr($gn, 0, 6) == 'group-' || substr($gn, 0, 7) == 'domain-') && !empty($desc)) {
                                $groups[] = $gn;
                            }
                        }
                    }
                    $detail_log[] = '使用者先前已加入以下群組：';
                    foreach ($groups as $g) {
                        $detail_log[] = $g;
                    }
                    $detail_log[] = '現在正在更新使用者資訊中......';
                    $user = $this->sync_user($t, $user_key, $user, $password_sync);
                    if ($user) {
                        $detail_log[] = '更新完成！';
                    } else {
                        $detail_log[] = "$t->role_name $t->realname 更新失敗！";
                    }
                } else {
                    $detail_log[] = '無法在 G Suite 中找到使用者，現在正在為使用者建立帳號......';
                    $user = $this->sync_user($t, $user_key);
                    if ($user) {
                        $detail_log[] = '建立完成！';
                    } else {
                        $detail_log[] = "$t->role_name $t->realname 建立失敗！";
                    }
                }
                $units = $t->union();
                if (!empty($units)) {
                    foreach ($units as $unit) {
                        $detail_log[] = "正在處理部門群組 $unit->name ......";
                        $found = false;
                        if ($all_groups) {
                            foreach ($all_groups as $group) {
                                if ($group->getDescription() == $unit->name) {
                                    $found = true;
                                    break;
                                }
                            }
                        }
                        if ($found) {
                            $group_key = $group->getEmail();
                            $depgroup = explode('@', $group_key)[0];
                            $detail_log[] = "$depgroup => 在 G Suite 中找到匹配的 Google 群組！";
                        } else {
                            $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
                            $depgroup = 'group-' . $unit->unit_no;
                            $group_key = $depgroup . '@' . $domain;
                            $group = $this->create_group($group_key, $unit->name);
                            if ($group) {
                                $all_groups[] = $group;
                                $detail_log[] = '建立成功！';
                            } else {
                                $detail_log[] = "$unit->name 群組建立失敗！";
                            }
                        }
                        if (($k = array_search($group_key, $groups)) !== false) {
                            unset($groups[$k]);
                        } else {
                            $detail_log[] = "正在將使用者：$t->role_name $t->realname 加入到群組裡......";
                            $members = $this->add_member($group_key, $user_key);
                            if (!empty($members)) {
                                $detail_log[] = '加入成功！';
                            } else {
                                $detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $unit->name 群組！";
                            }
                        }
                    }
                } else {
                    $detail_log[] = "找不到使用者 $t->role_name $t->realname 所屬部門！";
                }
                $roles = [];
                if ($t->roles->isNotEmpty()) {
                    foreach ($t->roles as $role) {
                        if (substr($role->unit_id, 0, 3) != 'Z01') {
                            $roles[] = $role;
                        }
                    }
                }
                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        $detail_log[] = "正在處理職務群組 $role->name ......";
                        $found = false;
                        if ($all_groups) {
                            foreach ($all_groups as $group) {
                                if ($group->getDescription() == $role->name) {
                                    $found = true;
                                    break;
                                }
                            }
                        }
                        if ($found) {
                            $group_key = $group->getEmail();
                            $jobgroup = explode('@', $group_key)[0];
                            $detail_log[] = "$jobgroup => 在 G Suite 中找到匹配的 Google 群組！";
                        } else {
                            $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
                            $jobgroup = 'group-' . $role->role_no;
                            $group_key = $jobgroup . '@' . $domain;
                            $group = $this->create_group($group_key, $role->name);
                            if ($group) {
                                $all_groups[] = $group;
                                $detail_log[] = '建立成功！';
                            } else {
                                $detail_log[] = "$role->role_name 群組建立失敗！";
                            }
                        }
                        if (($k = array_search($group_key, $groups)) !== false) {
                            unset($groups[$k]);
                        } else {
                            $detail_log[] = "正在將使用者：$t->role_name $t->realname 加入到群組裡......";
                            $members = $this->add_member($group_key, $user_key);
                            if (!empty($members)) {
                                $detail_log[] = '加入成功！';
                            } else {
                                $detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $role->name 群組！";
                            }
                        }
                    }
                } else {
                    $detail_log[] = "找不到使用者 $t->role_name $t->realname 行政職務！";
                }
                $domains = $t->domains;
                if ($domains->isNotEmpty()) {
                    foreach ($domains as $domain_obj) {
                        $detail_log[] = "正在處理領域群組 $domain_obj->name ......";
                        $found = false;
                        if ($all_groups) {
                            foreach ($all_groups as $group) {
                                if ($group->getDescription() == $domain_obj->name) {
                                    $found = true;
                                    break;
                                }
                            }
                        }
                        if ($found) {
                            $group_key = $group->getEmail();
                            $domgroup = explode('@', $group_key)[0];
                            $detail_log[] = "$domgroup => 在 G Suite 中找到匹配的 Google 群組！";
                        } else {
                            $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
                            $domgroup = 'domain-' . $domain_obj->id;
                            $group_key = $domgroup . '@' . $domain;
                            $group = $this->create_group($group_key, $domain_obj->name);
                            if ($group) {
                                $all_groups[] = $group;
                                $detail_log[] = '建立成功！';
                            } else {
                                $detail_log[] = "$domain_obj->name 群組建立失敗！";
                            }
                        }
                        if (($k = array_search($group_key, $groups)) !== false) {
                            unset($groups[$k]);
                        } else {
                            $detail_log[] = "正在將使用者：$t->role_name $t->realname 加入到群組裡......";
                            $members = $this->add_member($group_key, $user_key);
                            if (!empty($members)) {
                                $detail_log[] = '加入成功！';
                            } else {
                                $detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $domain_obj->name 群組！";
                            }
                        }
                    }
                } else {
                    $detail_log[] = "找不到使用者 $t->role_name $t->realname 隸屬的領域！";
                }
                if (!empty($t->tutor_class)) {
                    $stdgroup = 'class-' . $t->tutor_class;
                    $group_key = $stdgroup . '@' . $domain;
                    $found = false;
                    if ($all_groups) {
                        foreach ($all_groups as $group) {
                            if (strcasecmp($group->getEmail(), $group_key) === 0) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ($found) {
                        $detail_log[] = "$stdgroup => 在 G Suite 中找到匹配的 Google 群組！";
                        if (($k = array_search($group_key, $groups)) !== false) {
                            unset($groups[$k]);
                        } else {
                            $detail_log[] = "正在將使用者： $t->role_name $t->realname 加入到群組裡，成為群組管理員......";
                            $members = $this->add_member($group_key, $user_key, 'MANAGER');
                            if (!empty($members)) {
                                $detail_log[] = '加入成功！';
                            } else {
                                $detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $t->tutor_class 學生群組！";
                            }
                        }
                    }
                    $grade = substr($t->tutor_class, 0, 1);
                    $detail_log[] = '正在處理 ' . $grade . '年級......';
                    switch ($grade) {
                        case 1:
                            $clsgroup = 'group-ca';
                            break;
                        case 2:
                            $clsgroup = 'group-cb';
                            break;
                        case 3:
                            $clsgroup = 'group-cc';
                            break;
                        case 4:
                            $clsgroup = 'group-cd';
                            break;
                        case 5:
                            $clsgroup = 'group-ce';
                            break;
                        case 6:
                            $clsgroup = 'group-cf';
                            break;
                        default:
                            $clsgroup = 'group-c' . $grade;
                    }
                    $group_key = $clsgroup . '@' . $domain;
                    $found = false;
                    if ($all_groups) {
                        foreach ($all_groups as $group) {
                            if (strcasecmp($group->getEmail(), $group_key) === 0) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ($found) {
                        $detail_log[] = "$clsgroup => 在 G Suite 中找到匹配的 Google 群組！";
                    } else {
                        $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
                        $group = $this->create_group($group_key, $grade . '年級');
                        if ($group) {
                            $detail_log[] = '建立成功！';
                        } else {
                            $detail_log[] = "$grade 年級群組建立失敗！";
                        }
                    }
                    if (($k = array_search($group_key, $groups)) !== false) {
                        unset($groups[$k]);
                    } else {
                        $detail_log[] = "正在將使用者： $t->role_name $t->realname 加入到群組裡......";
                        $members = $this->add_member($group_key, $user_key);
                        if (!empty($members)) {
                            $detail_log[] = '加入成功！';
                        } else {
                            $detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $grade 年級群組！";
                        }
                    }
                }
                foreach ($groups as $g) {
                    $detail_log[] = "正在將使用者：$t->role_name $t->realname 從群組 $g 移除......";
                    $result = $this->remove_member($g, $user_key);
                    if ($result) {
                        $detail_log[] = '移除成功！';
                    } else {
                        $detail_log[] = "無法將使用者 $t->role_name $t->realname 從群組 $g 移除！";
                    }
                }
            }
        }
        return $detail_log;
    }

    public function sync_grade($grade, $password_sync = false)
    {
        $detail_log = [];
        $domain = config('services.gsuite.domain');
        $classes = Grade::find($grade)->classrooms;
        if (!empty($classes)) {
            foreach ($classes as $c) {
                $stdgroup = 'class-' . $c->id;
                $group_key = $stdgroup . '@' . $domain;
                $detail_log[] = "正在處理 $c->name......";
                $group = $this->get_group($group_key);
                if ($group) {
                    $detail_log[] = "$stdgroup => 在 G Suite 中找到匹配的 Google 群組！......";
                    $members = $this->list_members($group_key);
                    foreach ($members as $u) {
                        $this->remove_member($group_key, $u->getEmail());
                    }
                    $detail_log[] = '已經移除群組裡的所有成員！';
                } else {
                    $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
                    $group = $this->create_group($group_key, $c->name);
                    if ($group) {
                        $detail_log[] = '建立成功！';
                    } else {
                        $detail_log[] = "$c->name 群組建立失敗！";
                    }
                }
                $tutors = $c->tutors;
                if (!empty($tutors)) {
                    foreach ($tutors as $t) {
                        if (!empty($t->email)) {
                            $user_key = $t->email;
                            list($account, $path) = explode('@', $user_key);
                            if ($domain != $path) {
                                $user_key = $t->account . '@' . $domain;
                            }
                        } else {
                            $user_key = $t->account . '@' . $domain;
                        }
                        $user = $this->get_user($user_key);
                        if ($user) {
                            $detail_log[] = "正在將導師：$t->realname 加入到 $c->name 群組裡....";
                            $members = $this->add_member($group_key, $user_key, 'MANAGER');
                            if (!empty($members)) {
                                $detail_log[] = '加入成功！';
                            } else {
                                $detail_log[] = "將導師 $t->realname 加入 $c->name 群組失敗！";
                            }
                        }
                    }
                }
                $students = $c->students;
                if (!empty($students)) {
                    foreach ($students as $s) {
                        $user_alias = $s->id . '@' . $domain;
                        $user_key = 'meps' . $s->id . '@' . $domain;
                        $detail_log[] = "正在處理 $s->class $s->seat $s->realname ($user_key)......";
                        $user = $this->get_user($user_key);
                        if (!$user) {
                            $result = $this->find_users('externalId=' . $s->id);
                            if ($result) {
                                $user = $result[0];
                                $user_key = $user->getPrimaryEmail();
                            } else {
                                $result = $this->find_users('externalId=' . $s->uuid);
                                if ($result) {
                                    $user = $result[0];
                                    $user_key = $user->getPrimaryEmail();
                                }
                            }
                        }
                        if ($user) {
                            $detail_log[] = '在 G Suite 中找到這位使用者，正在更新使用者資訊中......';
                            $user = $this->sync_user($s, $user_key, $user, $password_sync);
                            if ($user) {
                                $detail_log[] = '更新完成！';
                            } else {
                                $detail_log[] = "$c->id $s->seat $s->realname 更新失敗！";
                            }
                        } else {
                            $detail_log[] = '無法在 G Suite 中找到使用者，現在正在為使用者建立帳號......';
                            $user = $this->sync_user($s, $user_key);
                            if ($user) {
                                $detail_log[] = '建立完成！';
                            } else {
                                $detail_log[] = "$c->id $s->seat $s->realname 建立失敗！";
                            }
                        }
                        $detail_log[] = "正在建立使用者別名 $user_alias ......";
                        if ($this->has_alias($user_key, $user_alias)) {
                            $detail_log[] = '別名已經存在！';
                        } else {
                            $result = $this->create_alias($user_key, $user_alias);
                            if ($result) {
                                $detail_log[] = '建立完成！';
                            } else {
                                $detail_log[] = '別名建立失敗！';
                            }
                        }
                        $detail_log[] = "正在將使用者：$c->id $s->seat $s->realname 加入到 $c->name 群組裡....";
                        $members = $this->add_member($group_key, $user_key);
                        if (!empty($members)) {
                            $detail_log[] = '加入成功！';
                        } else {
                            $detail_log[] = "將 $c->id $s->seat $s->realname 加入 $c->name 群組失敗！";
                        }
                    }
                }
            }
        }
        return $detail_log;
    }

    public function sync_class($class_id, $password_sync = false)
    {
        $detail_log = [];
        $domain = config('services.gsuite.domain');
        $myclass = Classroom::find($class_id);
        $stdgroup = 'class-' . $myclass->id;
        $group_key = $stdgroup . '@' . $domain;
        $detail_log[] = '正在處理 ' . $myclass->name . '.....';
        $group = $this->get_group($group_key);
        if ($group) {
            $detail_log[] = "$stdgroup => 在 G Suite 中找到匹配的 Google 群組！......";
            $members = $this->list_members($group_key);
            foreach ($members as $u) {
                $this->remove_member($group_key, $u->getEmail());
            }
            $detail_log[] = '已經移除群組裡的所有成員！';
        } else {
            $detail_log[] = '無法在 G Suite 中找到匹配的群組，現在正在建立新的 Google 群組......';
            $group = $this->create_group($group_key, $myclass->name);
            if ($group) {
                $detail_log[] = '建立成功！';
            } else {
                $detail_log[] = "$myclass->name 群組建立失敗！";
            }
        }
        $tutors = $myclass->tutors;
        if (!empty($tutors)) {
            foreach ($tutors as $t) {
                if (!empty($t->email)) {
                    $user_key = $t->email;
                    list($account, $path) = explode('@', $user_key);
                    if ($domain != $path) {
                        $user_key = $t->account . '@' . $domain;
                    }
                } else {
                    $user_key = $t->account . '@' . $domain;
                }
                $user = $this->get_user($user_key);
                if ($user) {
                    $detail_log[] = "正在將導師：$t->realname 加入到 $myclass->name 群組裡....";
                    $members = $this->add_member($group_key, $user_key, 'MANAGER');
                    if (!empty($members)) {
                        $detail_log[] = '加入成功！';
                    } else {
                        $detail_log[] = "將導師 $t->realname 加入 $myclass->name 群組失敗！";
                    }
                }
            }
        }
        $students = $myclass->students;
        if (!empty($students)) {
            foreach ($students as $s) {
                $user_alias = $s->id . '@' . $domain;
                $user_key = 'meps' . $s->id . '@' . $domain;
                $detail_log[] = "正在處理 $s->class $s->seat $s->realname ($user_key)......";
                $user = $this->get_user($user_key);
                if (!$user) {
                    $result = $this->find_users('externalId=' . $s->id);
                    if ($result) {
                        $user = $result[0];
                        $user_key = $user->getPrimaryEmail();
                    } else {
                        $result = $this->find_users('externalId=' . $s->uuid);
                        if ($result) {
                            $user = $result[0];
                            $user_key = $user->getPrimaryEmail();
                        }
                    }
                }
                if ($user) {
                    $detail_log[] = '在 G Suite 中找到這位使用者，正在更新使用者資訊中......';
                    $user = $this->sync_user($s, $user_key, $user, $password_sync);
                    if ($user) {
                        $detail_log[] = '更新完成！';
                    } else {
                        $detail_log[] = "$s->class $s->seat $s->realname 更新失敗！";
                    }
                } else {
                    $detail_log[] = '無法在 G Suite 中找到使用者，現在正在為使用者建立帳號......';
                    $user = $this->sync_user($s, $user_key);
                    if ($user) {
                        $detail_log[] = '建立完成！';
                    } else {
                        $detail_log[] = "$s->class $s->seat $s->realname 建立失敗！";
                    }
                }
                $detail_log[] = "正在建立使用者別名 $user_alias ......";
                if ($this->has_alias($user_key, $user_alias)) {
                    $detail_log[] = '別名已經存在！';
                } else {
                    $result = $this->create_alias($user_key, $user_alias);
                    if ($result) {
                        $detail_log[] = '建立完成！';
                    } else {
                        $detail_log[] = '別名建立失敗！';
                    }
                }
                $detail_log[] = "正在將使用者：$myclass->id $s->seat $s->realname 加入到 $myclass->name 群組裡....";
                $members = $this->add_member($group_key, $user_key);
                if (!empty($members)) {
                    $detail_log[] = '加入成功！';
                } else {
                    $detail_log[] = "將 $myclass->id $s->seat $s->realname 加入 $myclass->name 群組失敗！";
                }
            }
        }
        return $detail_log;
    }

    public function deal_graduate($leave, $target)
    {
        $detail_log = [];
        if ($leave == 'suspend') {
            $students = $this->find_users("email:$target* isSuspended=false");
        } else {
            $students = $this->find_users("email:$target*");
        }
        foreach ($students as $s) {
            $realname = $s->getName()->getFullName();
            $org = $s->getOrganizations();
            $org_title = '';
            if ($org && $org[0])
                $org_title = $org[0]['title'];
            $user_key = $s->getPrimaryEmail();
            $detail_log[] = "在 G Suite 中找到畢業生 $org_title $realname ($user_key)......";
            if ($leave == 'suspend') {
                $detail_log[] = '正在停用帳號中......';
                $user = $this->suspend_user($user_key);
                if ($user) {
                    $detail_log[] = '已停用！';
                } else {
                    $detail_log[] = "$org_title $realname 停用失敗！";
                }
            } elseif ($leave == 'remove') {
                $detail_log[] = '正在移除帳號中......';
                $result = $this->delete_user($user_key);
                if ($result) {
                    $detail_log[] = '已移除！';
                } else {
                    $detail_log[] = "$org_title $realname 移除失敗！";
                }
            }
        }
        return $detail_log;
    }

}