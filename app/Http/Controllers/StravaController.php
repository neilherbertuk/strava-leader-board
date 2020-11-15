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
        $users = StravaUser::get()->sortByDesc('total_distance_meters');

        $first = true;
        $users = $users->each(function($user) use (&$first){
            if ($first){
                $leader_for = Carbon::parse($user->last_took_lead)->diffInSeconds(Carbon::now());
                $user->time_in_lead_hum = $this->secondsToTime($leader_for);
                $user->total_time_in_lead = $user->time_in_lead + $leader_for;
                $first = false;
            } else {
                $user->total_time_in_lead = $user->time_in_lead;
            }

            $user->total_time_in_lead_hum = $this->secondsToTime($user->total_time_in_lead);
        });

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
