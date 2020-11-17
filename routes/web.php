<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', \App\Http\Controllers\StravaController::class .'@index');
Route::get('/strava/auth', \App\Http\Controllers\StravaController::class .'@auth');
Route::get('/strava/callback', \App\Http\Controllers\StravaController::class .'@authCallback');
Route::post('/push',\App\Http\Controllers\PushController::class .'@store');
Route::get('/push/success/{guest_id}',\App\Http\Controllers\PushController::class .'@success');
