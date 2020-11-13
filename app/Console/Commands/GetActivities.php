<?php

namespace App\Console\Commands;

use App\Models\StravaActivity;
use App\Models\StravaUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Strava;

class GetActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:getactivities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all activities';

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
        Log::info('Getting Strava Activities');
        Log::info('Getting Users');
        $users = StravaUser::get();

        if (count($users)  == 0) {
            Log::info('No users found');
        } else {
            Log::info(count($users) . ' user(s) found)');
            $users->each(function ($user) {
                Log::info('Getting activities for '. $user->id);
                // Make sure Strava API Token is valid
                if (Carbon::now() > $user->token_expires) {
                    Log::info('API Token has expired, refreshing');
                    // Token has expired, generate new tokens using the currently stored user refresh token
                    $refresh = Strava::refreshToken($user->refresh_token);

                    $user->update([
                        'access_token' => $refresh->access_token,
                        'refresh_token' => $refresh->refresh_token,
                        'token_expires' => Carbon::createFromTimestamp($refresh->expires_at)
                    ]);

                    $user->save();
                }

                // Set starting date to get activities from if not set
                if (empty($user->activities_until)) {
                    Log::info('Not seen activities for this user before, getting activities since 2020-11-09');
                    $user->activities_until = Carbon::createMidnightDate('2020', '11', '09');
                    $user->save();
                }

                $now = Carbon::now()->timestamp;
                $activities = collect(Strava::activities($user->access_token, 1, 100, $now, ($user->activities_until->timestamp - 1)));

                if(count($activities) == 0) {
                    Log::info('No activities found for user');
                } else {
                    Log::info(count($activities) .' activities found');
                    $activities->each(function ($activity) use ($user) {
                        if (count($user->activities()->where('id', '=', $activity->id)->get()) > 0) {
                            Log::info('Strava Activity ' . $activity->id . ' already exists');
                        } else {
                            Log::info('Creating activity '. $activity->id);
                            StravaActivity::create([
                                'id' => $activity->id,
                                'strava_user_id' => $user->strava_id,
                                'name' => $activity->name,
                                'distance' => $activity->distance,
                                'moving_time' => $activity->moving_time,
                                'elapsed_time' => $activity->elapsed_time,
                                'total_elevation_gain' => $activity->total_elevation_gain,
                                'type' => strtolower($activity->type),
                                'start_date' => Carbon::parse($activity->start_date),
                                'start_date_local' => Carbon::parse($activity->start_date_local),
                                'utc_offset' => $activity->utc_offset,
                                'average_speed' => $activity->average_speed,
                                'max_speed' => $activity->max_speed
                            ]);
                        }
                    });
                    $user->activities_until = $now;
                    $user->save();
                }

                Log::info('Finished logging activities for '. $user->id);
            });
        }

        // Generate updated at and next update timestamps
        $now = Carbon::now();
        Cache::store('file')->put('strava_get_activities_time', $now->toDateTimeString());
        $now = $now->addMinutes(15);
        $start = Carbon::createFromTimeString('05:00');
        $end = Carbon::createFromTimeString('23:00');

        if ($now->between($start, $end)) {
            Cache::store('file')->put('strava_next_activities_time', $now->addMinutes(15)->toDateTimeString());
        } else {
            if ($now->isBefore($start)) {
                Cache::store('file')->put('strava_next_activities_time', Carbon::create($now->year,$now->month,$now->day,5,0,0)->toDateTimeString());
            } else {
                Cache::store('file')->put('strava_next_activities_time', Carbon::create($now->year,$now->month,$now->day,5,0,0)->addDay()->toDateTimeString());
            }
        }
        return 0;
    }
}
