<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lockdown v2 Challenge Leader Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            padding: 0;
            color: white;
            background: #1a202c;
            box-sizing: border-box;
            font-family: Nunito;
            font-size: 15px;
        }
        a, a:active {
            color: #fff;
            text-decoration: none;
        }
        a:hover {
            color: #c8c8c8;
            text-decoration: none;
        }
        .grid-container {
            display: grid; /* applies grid layout affecting it's children */
            grid-template-columns: 1fr; /* define the number and sizes of columns */
            grid-template-rows: 50px 1fr 50px; /* defines the number ans sizes of rows*/
            grid-template-areas:
                'header'
                'main'
                'footer'; /* assigns the labeled grid-area to the grid layout, note there has to be two columns and three rows as per the grid template rows/ columns defined */
            height: 100vh;
        }

        .header {
            grid-area: header; /* assigns the grid-area label*/
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
            padding: 0 15px;
        }

        .main {
            grid-area: main;
            background-color: #2d3748;
        }

        .main_cards{
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .main_athlete_card {
            flex-basis: 250px;
            flex-grow: 1;
            margin: 10px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            height: 300px;
            border: 0 solid #e2e8f0;
            border-radius: 8px;
            background: #1a202c;
            color: #fff;
        }

        .main_about_card {
            flex-basis: 100%;
            flex-grow: 1;
            margin: 10px 10px;
            padding: 20px;
            border: 0 solid #e2e8f0;
            border-radius: 8px;
            background: #1a202c;
            color: #fff;
        }

        .break {
            flex-basis: 100%;
            height: 0;
        }

        .avatar {
            border-radius: 50%;
            width: 75px;
            height: 75px;
        }

        .avatar-container {
            position: relative;
            width: 75px;
            height: 75px;
            border-radius: 50%;
            overflow: hidden;
        }

        .img-text {
            position: absolute;
            top: 0px;
            left: 0px;
            height:70px;
            width: 75px;
            background-color:rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 1.5em;
            font-weight: bold;
            padding-left: 30px;
            padding-top: 5px;
        }

        .footer {
            grid-area: footer;
            background-color: whitesmoke;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: darkblue;
            padding: 0 15px;
        }

        .footer a, .footer a:active {
            color: darkblue;
            text-decoration: none;
        }
        a:hover {
            color: #c8c8c8;
            text-decoration: none;
        }

        .footer_byline {
            font-size:0.75em;
        }
    </style>
</head>
<body>
<div class="grid-container">
    <header class="header">
        <div class="header_title">Lockdown v2 Challenge Leader Board</div>
        <div class="header_right"><a href="#" onclick="initSW()"><i class="fa fa-bell" aria-hidden="true"></i></a></div>
    </header>
    <main class="main">
        <div class="main_cards">
            @foreach($users as $user)
            <div class="main_athlete_card">
                <div class="main_athlete_card-icon">
                    @if (!empty($user->profile_link))
                    <a href="{{ $user->profile_link }}">
                    @endif
                    <div class="avatar-container"><img src="{{ $user->avatar }}" class="avatar"><div class="img-text"><p>{{ ($loop->index + 1) }}</p></div></div><br />
                    {{ $user->username }}
                    @if (!empty($user->profile_link))
                    </a>
                    @endif
                    @if ($loop->index == 0)
                    <br/><br /><b>Time Since Taking Lead</b><br />
                    {{ $user->time_in_lead_hum }}
                    @endif
                    <br/><br/><b>Total Time As Leader</b><br/>
                    {{ $user->total_time_in_lead_hum }}
                </div>
                <div class="main_athlete_card-info" style="margin-left:30px">
                    <b>Total Distance</b><br />
                    {{ $user->total_distance_miles }} miles<br /><br />
                    <b>Max Speed</b><br />
                    {{ round($user->max_speed / 1.467, 2) }} mph<br /><br />
                    <b>Moving Time</b><br />
                    {{ $user->total_moving_time_hum }}<br /><br />
                    <b>Activities</b><br />
                    {{ $user->total_activities ?? '0' }} activities<br />
                    {{ $user->run_count ?? '0' }} runs<br />
                    {{ $user->walk_count ?? '0' }} walks<br />
                </div>
            </div>
            @endforeach
            @if(count($users) == 0)
            <div class="main_athlete_card">
                <div class="main_athlete_card-info">There currently isn't anyone on the leader board</div>
            </div>
            @endif
        </div>
        <div class="main_cards">
            <div class="main_about_card">
                <div><h2>Erm, what exactly is this?!?</h2></div>
                <div class="break"></div>
                <div>
                    <p>The short answer; there's nothing quite like a bit of sibling rivalry!</p>
                    <p>I'm Neil, based in the UK and over the past year I've been working from home, so have erm, put on a bit of extra weight. Now that the UK has gone back into a 2nd lockdown, my sister has challenged me see who can do the most miles between November 9th and the end of lockdown. We're using Strava to track our walks and runs.</p>
                    <p>Dashboard has been lovingly built with <a href="https://www.laravel.com">Laravel</a> &hearts; and CSS Flexbox. Data is fetched from Strava's API every 15 minutes between 05:00 and 23:00.</p>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="footer_copyright">&copy;2020 <a href="https://github.com/neilherbertuk/strava-leader-board"><i class="fa fa-github" aria-hidden="true"></i></a> <a href="https://twitter.com/NeilHerbert"><i class="fa fa-twitter" aria-hidden="true"></i></a></div>
        <div class="footer_byline">Last Updated @ {{ $strava_get_activities_time }}<br/>Next Update @ {{ $strava_next_activities_time }}</div>
    </footer>
    <script src="{{ asset('js/enable-push.js') }}" defer></script>
</div>
</body>
</html>
