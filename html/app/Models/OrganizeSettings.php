<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizeSettings extends Model
{

    protected $table = 'organize_settings';

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

    public function onSurvey()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->survey_at);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->first_stage);
        return Carbon::now()->between($startDate, $endDate);
    }

    public function onFirstStage()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->first_stage);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->pause_at);
        return Carbon::now()->between($startDate, $endDate);
    }

    public function onPause()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->pause_at);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->second_stage);
        return Carbon::now()->between($startDate, $endDate);
    }

    public function onSecondStage()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->second_stage);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->close_at);
        return Carbon::now()->between($startDate, $endDate);
    }

    public function onFinish()
    {
        $endDate = Carbon::createFromFormat('Y-m-d', $this->close_at);
        return Carbon::now() > $endDate;
    }

}
