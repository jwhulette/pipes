<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTestTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('test');
    }
}
