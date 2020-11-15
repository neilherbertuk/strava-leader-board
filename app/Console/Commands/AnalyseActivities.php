<?php

namespace App\Console\Commands;

use App\Models\StravaUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnalyseActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:analyseactivities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse activities for athletes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Loop through users updating stats
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

            // Update the leader board
            $users = $users->sortByDesc('total_distance_meters');// Sort by largest distance
            $leader_changed = false;
            $isFirst = true;
            $leader_activity_time = null;
            $users->each(function($user) use (&$leader_changed, &$isFirst, &$leader_activity_time){
                // Is this the first entry (leader)
                if ($isFirst) {
                    // Was this already the leader?
                    if (!$user->is_in_lead){
                        $user->is_in_lead = true;
                        $user->last_took_lead = $user->activities_until;
                        $leader_activity_time = $user->activities_until;
                        $leader_changed = true;
                    }
                    $isFirst = false;
                } else {
                    // Has the leader changed?
                    if ($leader_changed) {
                        // Was this athlete the previous leader?
                        if ($user->is_in_lead) {
                            // Increase time in lead
                            $user->time_in_lead += $user->last_took_lead->diffInSeconds($leader_activity_time);
                            $user->time_in_lead_hum = $this->secondsToTime($user->time_in_lead);
                        }
                        $user->is_in_lead = false;
                    }
                }

               $user->save();
            });
        }
        return 0;
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
