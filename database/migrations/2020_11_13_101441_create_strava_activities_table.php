<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strava_activities', function (Blueprint $table) {
            $table->id();
            $table->string('strava_user_id');
            $table->string('name');
            $table->integer('distance');
            $table->integer('moving_time');
            $table->integer('elapsed_time');
            $table->integer('total_elevation_gain');
            $table->string('type');
            $table->timestamp('start_date');
            $table->timestamp('start_date_local');
            $table->integer('utc_offset');
            $table->integer('average_speed');
            $table->integer('max_speed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strava_activities');
    }
}
