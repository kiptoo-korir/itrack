<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAccessTokenTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('last_seen')->nullable();
            });
        }
        if (Schema::hasTable('access_tokens')) {
            Schema::table('access_tokens', function (Blueprint $table) {
                $table->boolean('verified')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
