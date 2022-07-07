<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Providers\TpedussoServiceProvider as SSO;

class Student extends Model
{

	protected $table = 'students';
	protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'idno',
        'account',
        'sn',
        'gn',
        'realname',
        'class',
        'seat',
        'birthdate',
        'gender',
        'email',
        'mobile',
        'telephone',
        'address',
        'www',
        'character',
        'fetch_date',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

    public function expired()
	{
    	return Carbon::today() > new Carbon($this->updated_at)->addDays(config('app.expired_days'));
	}

}
