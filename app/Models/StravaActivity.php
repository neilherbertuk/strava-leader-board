<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StravaActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'strava_user_id',
        'name',
        'distance',
        'moving_time',
        'elapsed_time',
        'total_elevation_gain',
        'type',
        'start_date',
        'start_date_local',
        'utc_offset',
        'average_speed',
        'max_speed'
        ];

    protected $dates = ['start_date', 'start_date_local'];

}
