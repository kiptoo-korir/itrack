<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('base_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('platforms');
    }
}
