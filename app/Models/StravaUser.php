<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StravaUser extends Model
{
    use HasFactory;

    protected $fillable = ['strava_id', 'username', 'access_token', 'refresh_token', 'token_expires', 'activities_until', 'avatar'];

    protected $dates = ['token_expires', 'activities_until'];

    protected $hidden = ['access_token', 'refresh_token', 'token_expires'];

    public function activities()
    {
        return $this->hasMany(StravaActivity::class, 'strava_user_id', 'strava_id');
    }
}
