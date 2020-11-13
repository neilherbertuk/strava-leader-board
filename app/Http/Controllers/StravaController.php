<?php

namespace App\Http\Controllers;

use App\Models\StravaActivity;
use App\Models\StravaUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Strava;

class StravaController extends Controller
{
    public function index()
    {
        $users = StravaUser::get();
        if (count($users) > 0){
            $users->each(function($user){
                $user->total_distance_meters = $user->activities->sum('distance');
                $user->total_distance_miles = round(($user->total_distance_meters / 1609), 2);
                $user->total_moving_time = $user->activities->sum('moving_time');
                $user->total_moving_time_hum = $this->secondsToTime($user->activities->sum('moving_time'));
                $user->total_activities = count($user->activities);
                $user->walk_count = count($user->activities()->where('type', '=', 'walk')->get());
                $user->run_count = count($user->activities()->where('type', '=', 'run')->get());
                $user->max_speed = $user->activities->max('max_speed');
                $user->profile_link = 'https://www.strava.com/athletes/'. $user->strava_id;
                return $user;
            });
            $users = $users->sortByDesc('total_distance_meters');
        }
        return view('dashboard', ['users' => $users, 'strava_get_activities_time' => Cache::store('file')->get('strava_get_activities_time', 'unknown'), 'strava_next_activities_time' => Cache::store('file')->get('strava_next_activities_time', 'unknown')]);
    }

    /**
     * Authenticate user with Strava
     *
     * @return mixed
     */
    public function auth()
    {
        if (!env('ALLOW_STRAVA_AUTH', false)) {
            return redirect("/");
        }
        return Strava::authenticate($scope='read_all,profile:read_all,activity:read_all');
    }

    /**
     * Get token from auth callback
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function authCallback(Request $request)
    {
        if (!env('ALLOW_STRAVA_AUTH', false)) {
            return redirect("/");
        }

        // Get access token
        try {
            $token = Strava::token($request->code);
        } catch (\Exception $e) {
            return "Authentication error. Please try again.";
        }

        // Create or update user
        $user = StravaUser::firstOrCreate(['strava_id' => $token->athlete->id]);
        $user->username = $token->athlete->username;
        $user->access_token = $token->access_token;
        $user->refresh_token = $token->refresh_token;
        $user->token_expires = Carbon::createFromTimestamp($token->expires_at);
        $user->avatar = $token->athlete->profile;
        $user->save();
        return redirect('/');
    }

    protected function secondsToTime($inputSeconds) {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // Extract days
        $days = floor($inputSeconds / $secondsInADay);

        // Extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // Extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // Extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
        ];

        foreach ($sections as $name => $value){
            if ($value > 0){
                $timeParts[] = $value. ' '.$name.($value == 1 ? '' : 's');
            }
        }

        return implode(', ', $timeParts);
    }
}
