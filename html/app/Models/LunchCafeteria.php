<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchCafeteria extends Model
{
    use HasFactory;

    protected $table = 'lunch_cafeterias';

    protected $fillable = [
        'description',
    ];
}
