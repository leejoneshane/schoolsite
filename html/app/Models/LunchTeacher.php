<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchTeacher extends Model
{
    use HasFactory;

    protected $table = 'lunch_teachers';

    protected $fillable = [
        'section',
        'uuid',
        'tutor',
        'weekdays',
        'places',
        'vegen',
        'milk',
    ];

    protected $casts = [
        'tutor' => 'boolean',
        'weekdays' => 'array',
        'places' => 'array',
        'vegen' => 'boolean',
        'milk' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'uuid', 'uuid'); // Assuming Teacher model matches uuid
    }
}
