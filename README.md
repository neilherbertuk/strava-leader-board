## Strava Leader Board
This is a Strava Leader Board dashboard built with Laravel 8.x using CSS Flexbox.

<img width="500" alt="Screenshot 2020-11-13 at 23 25 54" src="https://user-images.githubusercontent.com/5868029/99130260-bf3cee00-2607-11eb-8e9f-88962da2ded3.png">

<img width="200" alt="Screenshot 2020-11-13 at 23 29 02" src="https://user-images.githubusercontent.com/5868029/99130379-0dea8800-2608-11eb-865f-f64bc80f37c5.png">

## Getting Started

The environment needed for this application to run is the same as the minimum spec needed by Laravel 8.x.

Clone the repo

Install PHP package dependencies

```bash
composer install
```

Setup configuration
```bash
cp .env.example .env
php artisan key:generate
php artisan webpush:vapid (for push notifications)
```

Setup database
```bash
touch database\database.sqlite
php artisan migrate
```

Setup cronjob - Add the following to your crontab file
```bash
crontab -e
```
```bash
* 5-22 * * * cd /path/to/repo && php artisan schedule:run
```

The cronjob will run every 15 minutes between 5 am and 11 pm.

Register an application with Strava's developer portal and update `.env` with the client_id and client_secret. Update the redirect URI entry to reflect the correct domain for your application.

Update the public push notification key within `public\js\enable-push.js` under `applicationServerKey`

### Adding Athletes

To allow athletes to enrol with your leader board, set `ALLOW_STRAVA_AUTH` to true - make sure you set this to false when done otherwise anyone will be able to add them-selves to your board.
Direct users to [https://your-domain.com/strava/auth](https://your-domain.com/strava/auth) to register.

I wouldn't recommend using this for too many people as Strava have API rate limits which I'm not error handling for.

### Sync with Strava

The cronjob you've created above will run every minute between the hours of 5am and 11pm. The `strava:getavtivities` artisan command is then run every 15 minutes (during minutes 0, 15, 30 and 45). This can be changed within the `app\Console\kernel.php` file.

You can manually sync with Strava by running `php artisan strava:getactivities` from the terminal.

You will also need to analyse the activities by running `php artisan strava:analyseactivities` which will update the stats used for the leader board. This will also send push notifications to users to let them know if there is a new leader.

## Notes
I'm using the package codetoad\laravel-strava to talk to the Strav API, however, this doesn't currently support Laravel 8, so I'm using a fork I've created for now.
 
## License

This dashboard is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
