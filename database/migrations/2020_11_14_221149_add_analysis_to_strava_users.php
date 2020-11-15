<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnalysisToStravaUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('strava_users', function (Blueprint $table) {
            $table->integer('total_distance_meters')->default(0);
            $table->integer('total_distance_miles')->default(0);
            $table->integer('total_moving_time')->default(0);
            $table->string('total_moving_time_hum')->default('0 seconds');
            $table->integer('total_activities')->default(0);
            $table->integer('walk_count')->default(0);
            $table->integer('run_count')->default(0);
            $table->integer('max_speed')->default(0);
            $table->string('profile_link')->nullable();
            $table->timestamp('last_took_lead')->nullable();
            $table->boolean('is_in_lead')->default(false);
            $table->integer('time_in_lead')->default(0);
            $table->string('time_in_lead_hum')->default('0 seconds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('strava_users', function (Blueprint $table) {
            $table->dropIfExists('total_distance_meters');
            $table->dropIfExists('total_distance_miles');
            $table->dropIfExists('total_moving_time');
            $table->dropIfExists('total_moving_time_hum');
            $table->dropIfExists('total_activities');
            $table->dropIfExists('walk_count');
            $table->dropIfExists('run_count');
            $table->dropIfExists('max_speed');
            $table->dropIfExists('profile_link');
            $table->dropIfExists('last_took_lead');
            $table->dropIfExists('is_in_lead');
            $table->dropIfExists('time_in_lead');
            $table->dropIfExists('time_in_lead_hum');
        });
    }
}
