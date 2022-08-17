<?php

namespace App\Providers;

use Log;
use Illuminate\Support\ServiceProvider;
use App\Models\Teacher;
use App\Models\Unit;

class ADServiceProvider extends ServiceProvider
{

	private $connect = null;
	private $bind = null;

    public function __construct()
    {
        if (is_null($this->connect)) {
			$this->init();
		}
    }

	public function init()
	{
		$ad_host = config('services.ad.server');
		$ad_port = config('services.ad.port');
		$ca_path = config('services.ad.ca_file');
		ldap_set_option(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
		if ($ca_path) {
			ldap_set_option(null, LDAP_OPT_X_TLS_CACERTFILE, $ca_path);
		}
		$this->connect = @ldap_connect("ldaps://$ad_host:$ad_port");
		ldap_set_option($this->connect, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->connect, LDAP_OPT_REFERRALS, 0);
		if ($this->connect) {
			$ad_user = config('services.ad.admin');
			$ad_pass = config('services.ad.password');
			$this->bind = @ldap_bind($this->connect, $ad_user, $ad_pass);
			if ($this->bind) {
				return true;
			} else {
				Log::error('AD server can not login with user/passwd！'.ldap_error($this->connect));
				return false;
			}
		} else {
			Log::error('AD server reject LDAPS connection.'.ldap_error($this->connect));
			return false;
		}
	}

	public function error()
	{
    	return ldap_error($this->connect);
	}

	public function find_group($desc)
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(&(objectClass=group)(description=$desc))";
		$result = @ldap_search($this->connect, $base_dn, $filter);
		if ($result) {
			$infos = @ldap_get_entries($this->connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
			return $data;
		} else {
			return false;
		}
	}
	
	public function get_group($group)
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(&(objectClass=group)(CN=$group))";
		$result = @ldap_search($this->connect, $base_dn, $filter);
		$data = [];
		if ($result) {
			$infos = @ldap_get_entries($this->connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
			return $data;
		} else {
			return false;
		}
	}
	
	public function create_group($group, $dn, $group_name)
	{
		$groupinfo = [];
		$groupinfo['objectClass'] = 'top';
		$groupinfo['objectClass'] = 'group';
		$groupinfo['cn'] = $group;
		$groupinfo['sAMAccountName'] = $group;
		$groupinfo['displayName'] = $group_name;
		$groupinfo['description'] = $group_name;
		$result = @ldap_add($this->connect, $dn, $groupinfo);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_group($dn)
	{
		$result = @ldap_delete($this->connect, $dn);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function add_member($dn, $userDn)
	{
		$result = @ldap_mod_add($this->connect, $dn, ['member' => $userDn]);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function remove_member($dn, $userDn)
	{
		$result = @ldap_mod_del($this->connect, $dn, ['member' => $userDn]);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function all_users()
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(&(objectCategory=person)(sAMAccountName=*))";
		$result = @ldap_search($this->connect, $base_dn, $filter);
		if ($result) {
			$users = @ldap_get_entries($this->connect, $result);
			return $users;
		}
		return false;
	}

	public function get_user($account)
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(sAMAccountName=$account)";
		$result = @ldap_search($this->connect, $base_dn, $filter);
		if ($result) {
			$data = [];
			$infos = @ldap_get_entries($this->connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
			return $data;
		}
		return false;
	}
	
	public function find_user($filter)
	{
		$base_dn = config('services.ad.users_dn');
		$result = @ldap_search($this->connect, $base_dn, $filter);
		if ($result) {
			$data = [];
			$infos = @ldap_get_entries($this->connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
			return $data;
		}
		return false;
	}
	
	public function create_user(Teacher $user, $dn)
	{
		$userinfo = [];
		$userinfo['objectClass'] = ['top', 'person', 'organizationalPerson', 'user'];
		$userinfo['cn'] = $user->account;
		$userinfo['sAMAccountName'] = $user->account;
		$userinfo['accountExpires'] = 0;
		$userinfo['userAccountControl'] = 66080; //0x10220
		$userinfo['userPassword'] = substr($user->idno, -6);
		$userinfo['unicodePwd'] = $this->encryption(substr($user->idno, -6));
		if ($user->sn && $user->gn) {
			$userinfo['sn'] = $user->sn;
			$userinfo['givenName'] = $user->gn;
		}
		$userinfo['displayName'] = $user->realname;
		$userinfo['description'] = $user->idno;
		$userinfo['department'] = $user->unit_name;
		$userinfo['title'] = $user->role_name;
		if ($user->email) {
			$userinfo['mail'] = $user->email;
			$userinfo['userPrincipalName'] = $user->email;
		}
		if ($user->mobile) {
			$userinfo['telephoneNumber'] = $user->mobile;
		}
		$result = @ldap_add($this->connect, $dn, $userinfo);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function sync_user(Teacher $user, $dn)
	{
		$userinfo = [];
		if ($user->sn && $user->gn) {
			$userinfo['sn'] = $user->sn;
			$userinfo['givenName'] = $user->gn;
		}
		$userinfo['displayName'] = $user->realname;
		$userinfo['description'] = $user->idno;
		$userinfo['department'] = $user->unit_name;
		$userinfo['title'] = $user->role_name;
		if ($user->email) {
			$userinfo['mail'] = $user->email;
			$userinfo['userPrincipalName'] = $user->email;
		}
		if ($user->mobile) {
			$userinfo['telephoneNumber'] = $user->mobile;
		}
		$result = @ldap_mod_replace($this->connect, $dn, $userinfo);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function lock_user($dn)
	{
		$userdata['userAccountControl'] = '0x10222';
		$result = @ldap_mod_replace($this->connect, $dn, $userdata);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function unlock_user($dn)
	{
		$userdata['userAccountControl'] = '0x10220';
		$result = @ldap_mod_replace($this->connect, $dn, $userdata);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function delete_user($dn)
	{
		$result = @ldap_delete($this->connect, $dn);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function change_account($dn, $new_account)
	{
		$result = @ldap_mod_replace($this->connect, $dn, ['sAMAccountName' => $new_account]);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function change_pass($dn, $password)
	{
		$userdata = [];
		$userdata['userPassword'] = $password;
		$userdata['unicodePwd'] = $this->encryption($password);
		$result = @ldap_mod_replace($this->connect, $dn, $userdata);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	private function encryption($newPassword)
	{
		$newPassword = '"'.$newPassword.'"';
		$len = strlen($newPassword);
		$newPassw = '';
		for ($i = 0; $i < $len; ++$i) {
			$newPassw .= "{$newPassword[$i]}\000";
		}
		return $newPassw;
	}

	public function sync_teachers($password_sync, $leave)
	{
		$detail_log = [];
		$base_dn = config('services.ad.users_dn');
		$depts = Unit::main();
		$all_users = $this->all_users();
		unset($all_users['count']);
		$teachers = Teacher::all();
		if (!empty($teachers)) {
			foreach ($teachers as $t) {
				$detail_log[] = "正在處理 $t->role_name $t->realname ($t->account)......";
				$groups = [];
				$user = $this->get_user($t->account);
				if (!$user) {
					$user = $this->find_user('description='.$t->idno);
				}
				if ($user) {
					for ($i=0; $i<count($all_users); $i++) {
						if ($old['description'] == $t->idno || $old['samacountname'] == $t->account) {
							unset($all_users[$i]);
						}
					}
					$detail_log[] = '在 AD 中找到這位使用者';
					$user_dn = $user['distinguishedname'][0];
					$groups = $user['memberof'];
					foreach ($groups as $k => $g) {
						if (substr($g, 0, 9) != 'CN=group-') {
							unset($groups[$k]);
						}
					}
					$detail_log[] = '使用者先前已加入以下群組：';
					foreach ($groups as $g) {
						$detail_log[] = $g;
					}
					$detail_log[] = '現在正在更新使用者資訊中......';
					$result = $this->sync_user($t, $user_dn);
					if ($password_sync) {
						$this->change_pass($user_dn, substr($t->idno, -6));
					}
					if ($result) {
						$detail_log[] = '更新完成！';
					} else {
						$detail_log[] = "$dept->name $t->realname 更新失敗！".ad_error();
					}

				} else {
					$detail_log[] = '無法在 AD 中找到使用者，現在正在為使用者建立帳號......';
					$user_dn = 'CN='.$t->account.",$base_dn";
					$result = $this->create_user($t, $user_dn);
					if ($result) {
						$detail_log[] = '建立完成！';
					} else {
						$detail_log[] = "$dept->name $t->realname 建立失敗！".ad_error();
					}
				}
				if ($t->units()) {
					foreach ($units as $unit) {
						$detail_log[] = "正在處理 $unit->name ......";
						$group = $this->find_group($unit->name);
						if ($group) {
							$group_dn = $group['distinguishedname'][0];
							$depgroup = $group['samaccountname'][0];
							$detail_log[] = "$group_dn => 在 AD 中找到匹配的使用者群組！";
						} else {
							$detail_log[] = '無法在 AD 中找到匹配的群組，現在正在建立新的使用者群組......';
							$depgroup = 'group-'.$unit->unit_no;
							$group_dn = "CN=$depgroup,$base_dn";
							$result = $this->create_group($depgroup, $group_dn, $unit->unit_name);
							if ($result) {
								$detail_log[] = '建立成功！';
							} else {
								$detail_log[] = "$unit->unit_name 群組建立失敗！".ad_error();
							}
						}
						if (is_array($groups) && ($k = array_search($group_dn, $groups)) !== false) {
							unset($groups[$k]);
						} else {
							$detail_log[] = "正在將使用者： $unit->unit_name $t->realname 加入到群組裡...";
							$result = $this->add_member($group_dn, $user_dn);
							if ($result) {
								$detail_log[] = '加入成功！';
							} else {
								$detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $unit->name 群組！".ad_error();
							}
						}
					}
				}
				if (!empty($t->tutor_class)) {
					$detail_log[] = '正在處理 '.substr($t->tutor_class, 0, 1).'年級......';
					$grade = substr($t->tutor_class, 0, 1);
					switch ($grade) {
						case 1:
							$clsgroup = 'group-Ca';
							break;
						case 2:
							$clsgroup = 'group-Cb';
							break;
						case 3:
							$clsgroup = 'group-Cc';
							break;
						case 4:
							$clsgroup = 'group-Cd';
							break;
						case 5:
							$clsgroup = 'group-Ce';
							break;
						case 6:
							$clsgroup = 'group-Cf';
							break;
						default:
							$clsgroup = 'group-C'.$grade;
					}
					$group_dn = "CN=$clsgroup,$base_dn";
					$group = $this->get_group($clsgroup);
					if ($group) {
						$detail_log[] = "$clsgroup => 在 AD 中找到匹配的使用者群組！......";
					} else {
						$detail_log[] = '無法在 AD 中找到匹配的群組，現在正在建立新的使用者群組......';
						$result = $this->create_group($clsgroup, $group_dn, "$grade年級");
						if ($result) {
							$detail_log[] = '建立成功！';
						} else {
							$detail_log[] = "$grade 年級群組建立失敗！".ad_error();
						}
					}
					if (is_array($groups) && ($k = array_search($group_dn, $groups)) !== false) {
						unset($groups[$k]);
					} else {
						$detail_log[] = "正在將使用者： $t->role_name $t->realname 加入到群組裡......";
						$result = $this->add_member($group_dn, $user_dn);
						if ($result) {
							$detail_log[] = '加入成功！';
						} else {
							$detail_log[] = "無法將使用者 $t->role_name $t->realname 加入 $grade 年級群組！".ad_error();
						}
					}
				}
				foreach ($groups as $g) {
					if ($log) {
						$detail_log[] = "正在將使用者： $t->role_name $t->realname 從群組 $g 移除......";
					}
					$result = $this->remove_member($g, $user_dn);
					if ($result) {
						$detail_log[] = '移除成功！';
					} else {
						$detail_log[] = "無法將使用者 $t->role_name $t->realname 從群組 $g 移除！".ad_error();
					}
				}
			}
        }
		if (!empty($all_users)) {
			$detail_log[] = '在 AD 中找到未同步帳號共'.count($all_users).'個。';
			foreach ($all_users as $old) {
				$user_dn = $old['distinguishedname'][0];
				if ($leave == 'suspend') {
					$detail_log[] = '現在正在停用未同步帳號'.$old['displayname'].'...';
					$result = $this->lock_user($user_dn);
					if ($result) {
						$detail_log[] = '帳號已停用！';
					} else {
						$detail_log[] = '停用失敗！';
					}
				} elseif ($leave == 'remove') {
					$detail_log[] = '現在正在移除未同步帳號'.$old['displayname'].'...';
					$result = $this->delete_user($user_dn);
					if ($result) {
						$detail_log[] = '帳號已移除！';
					} else {
						$detail_log[] = "移除失敗！";
					}
				}
			}
		}
		return $detail_log;
	}

}