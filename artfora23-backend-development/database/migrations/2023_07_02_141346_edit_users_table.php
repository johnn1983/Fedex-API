<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('inv_address')->nullable();
            $table->string('inv_address2')->nullable();
            $table->string('inv_zip')->nullable();
            $table->string('inv_city')->nullable();
            $table->string('inv_state')->nullable();
            $table->string('inv_country')->nullable();
            $table->integer('inv_phone')->nullable();
            $table->string('inv_email')->nullable();
            $table->string('inv_att')->nullable();
            $table->string('dev_address')->nullable();
            $table->string('dev_address2')->nullable();
            $table->string('dev_zip')->nullable();
            $table->string('dev_city')->nullable();
            $table->string('dev_state')->nullable();
            $table->string('dev_country')->nullable();
            $table->integer('dev_phone')->nullable();
            $table->string('dev_email')->nullable();
            $table->string('dev_att')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
