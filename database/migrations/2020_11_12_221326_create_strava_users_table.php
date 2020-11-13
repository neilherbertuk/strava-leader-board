<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strava_users', function (Blueprint $table) {
            $table->id();
            $table->string('strava_id')->nullable();
            $table->string('username')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('token_expires')->nullable();
            $table->timestamp('activities_until')->nullable();
            $table->string('avatar')->nullable();
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
        Schema::dropIfExists('strava_users');
    }
}
