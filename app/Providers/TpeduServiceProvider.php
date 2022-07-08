<?php

namespace App\Providers;

use Log;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

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
            self::$oauth = new Http\Client([
                'verify' => false,
                'base_uri' => config('services.tpedu.server'),
            ]);
		}
        self::$seme = $this->seme();
    }

	function current_seme()
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

	public function error()
    {
        return self::$error;
    }

	function get_tokens($auth_code)
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
			Log::error('oauth2 token response =>'.$response->getBody());	
			return false;
		}
	}

	function refresh_tokens()
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
            	Log::error('oauth2 token response =>'.$response->getBody());
            	return false;
        	}
    	}
	}

	function login()
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

	function who()
	{
		if (self::$access_token) {
			$response = self::$oauth->get($config->get('services.tpedu.endpoint.user'), [
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

	function profile()
	{
		if (self::$access_token) {
			$response = self::$oauth->get($config->get('services.tpedu.endpoint.profile'), [
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
				Log::error('oauth2 user response =>'.$response->getBody());
				return false;
			}
		}
		return false;
	}

	function api($which, array $replacement = [])
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
			$dataapi = str_replace('{school}', $config->get('services.tpedu.school'), $dataapi);
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
			'headers' => ['Authorization' => 'Bearer ' . config('services.tpedu.token'),
			'http_errors' => false,
		]);
		$json = json_decode($response->getBody());
		if ($response->getStatusCode() == 200) {
			return $json;
		} else {
			Log::error('oauth2 api response:'.$dataapi.'=>'.$response->getBody());
			return false;
		}
	}

}