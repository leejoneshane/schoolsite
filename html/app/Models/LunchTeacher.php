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

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid', 'uuid'); // Assuming User model matches uuid
    }
}
