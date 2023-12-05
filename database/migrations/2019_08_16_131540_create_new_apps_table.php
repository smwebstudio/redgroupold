<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('tel');
            $table->string('email');
            $table->integer('property_type_id');
            $table->integer('contract_type_id');
            $table->integer('state_id');
            $table->string('street');
            $table->string('building');
            $table->string('apartment');
            $table->integer('floor')->nullable();
            $table->integer('buildings_floor_count')->nullable();
            $table->integer('ceiling_height_id');
            $table->integer('rooms_number')->nullable();
            $table->integer('area')->nullable();
            $table->string('price')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('map_coordinates')->nullable();
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
        Schema::dropIfExists('new_apps');
    }
}
