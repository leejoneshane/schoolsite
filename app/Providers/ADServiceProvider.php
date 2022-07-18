<?php

namespace App\Providers;

use Log;
use App\Models\Teacher;
use Illuminate\Support\ServiceProvider;

class ADServiceProvider extends ServiceProvider
{

	private static $connect = null;
	private static $bind = null;

    public function __construct()
    {
        if (is_null(self::$connect)) {
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
		self::$connect = @ldap_connect("ldaps://$ad_host:$ad_port");
		ldap_set_option($ad_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ad_conn, LDAP_OPT_REFERRALS, 0);
		if ($ad_conn) {
			$ad_user = config('services.ad.admin');
			$ad_pass = config('services.ad.password');
			self::$bind = @ldap_bind(self::$connect, $ad_user, $ad_pass);
			if (self::$bind) {
				return true;
			} else {
				Log::error('AD server can not login with user/passwd！'.ldap_error(self::$connect));
				return false;
			}
		} else {
			Log::error('AD server reject LDAPS connection.'.ldap_error(self::$connect));
			return false;
		}
	}

	public function error()
	{
    	return ldap_error(self::$connect);
	}

	public function find_group($desc)
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(&(objectClass=group)(description=$desc))";
		$result = @ldap_search(self::$connect, $base_dn, $filter);
		if ($result) {
			$infos = @ldap_get_entries(self::$connect, $result);
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
		$result = @ldap_search(self::$connect, $base_dn, $filter);
		$data = [];
		if ($result) {
			$infos = @ldap_get_entries(self::$connect, $result);
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
		$result = @ldap_add(self::$connect, $dn, $groupinfo);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_group($dn)
	{
		$result = @ldap_delete(self::$connect, $dn);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function add_member($dn, $userDn)
	{
		$result = @ldap_mod_add(self::$connect, $dn, ['member' => $userDn]);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function remove_member($dn, $userDn)
	{
		$result = @ldap_mod_del(self::$connect, $dn, ['member' => $userDn]);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_user($account)
	{
		$base_dn = config('services.ad.users_dn');
		$filter = "(sAMAccountName=$account)";
		$result = @ldap_search(self::$connect, $base_dn, $filter);
		$data = [];
		if ($result) {
			$infos = @ldap_get_entries(self::$connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
		}
		return $data;
	}
	
	public function find_user($filter)
	{
		$base_dn = config('services.ad.users_dn');
		$result = @ldap_search(self::$connect, $base_dn, $filter);
		$data = [];
		if ($result) {
			$infos = @ldap_get_entries(self::$connect, $result);
			if ($infos['count'] > 0) {
				$data = $infos[0];
			}
		}
		return $data;
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
		$result = @ldap_add(self::$connect, $dn, $userinfo);
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
		$result = @ldap_mod_replace(self::$connect, $dn, $userinfo);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function lock_user($dn)
	{
		$userdata['userAccountControl'] = '0x10222';
		$result = @ldap_mod_replace(self::$connect, $dn, $userdata);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function unlock_user($dn)
	{
		$userdata['userAccountControl'] = '0x10220';
		$result = @ldap_mod_replace(self::$connect, $dn, $userdata);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function delete_user($dn)
	{
		$result = @ldap_delete(self::$connect, $dn);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function change_account($dn, $new_account)
	{
		$result = @ldap_mod_replace(self::$connect, $dn, ['sAMAccountName' => $new_account]);
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
		$result = @ldap_mod_replace(self::$connect, $dn, $userdata);
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

}