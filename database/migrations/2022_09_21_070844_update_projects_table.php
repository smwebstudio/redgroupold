<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('projects', 'currency_usd')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->float('currency_usd')->nullable();
            });
        }
        if(!Schema::hasColumn('projects', 'currency_eur')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->float('currency_eur')->nullable();
            });
        }
        if(!Schema::hasColumn('projects', 'currency_rub')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->float('currency_rub')->nullable();
            });
        }
        if(!Schema::hasColumn('projects', 'get_rate_type')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('get_rate_type')->default('rate_from_kb')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('projects', 'get_rate_type')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('get_rate_type');
            });
        }
        if(Schema::hasColumn('projects', 'currency_rub')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('currency_rub');
            });
        }
        if(Schema::hasColumn('projects', 'currency_usd')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('currency_usd');
            });
        }
        if(Schema::hasColumn('projects', 'currency_eur')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('currency_eur');
            });
        }
    }
}
