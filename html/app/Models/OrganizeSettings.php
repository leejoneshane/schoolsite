<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizeSettings extends Model
{

    protected $table = 'organize_settings';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'syear',
        'survey_at',
        'first_stage',
        'pause_at',
        'second_stage',
        'close_at',
    ];

    protected $casts = [
        'survey_at' => 'datetime:Y-m-d',
        'first_stage' => 'datetime:Y-m-d',
        'pause_at' => 'datetime:Y-m-d',
        'second_stage' => 'datetime:Y-m-d',
        'close_at' => 'datetime:Y-m-d',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    public static function current()
    {
        return OrganizeSettings::where('syear', current_year())->first();
    }

    public static function years()
    {
        return DB::table('organize_settings')->select(['syear'])->distinct()->get()->map(function ($item) {
            return $item->syear;
        })->toArray();
    }

    public function notStart()
    {
        return Carbon::now() < $this->survey_at;
    }

    public function onPeriod()
    {
        return Carbon::now()->between($this->survey_at, $this->close_at);
    }

    public function onSurvey()
    {
        return Carbon::now()->between($this->survey_at, $this->first_stage);
    }

    public function onFirstStage()
    {
        return Carbon::now()->between($this->first_stage, $this->pause_at);
    }

    public function onPause()
    {
        return Carbon::now()->between($this->pause_at, $this->second_stage);
    }

    public function onSecondStage()
    {
        return Carbon::now()->between($this->second_stage, $this->close_at);
    }

    public function onFinish()
    {
        return Carbon::now() > $this->close_at;
    }

}
