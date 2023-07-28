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
        
            $table->string('sel_name')->nullable();
            $table->string('sel_address')->nullable();
            $table->string('sel_address2')->nullable();
            $table->string('sel_state')->nullable();
            $table->string('sel_country')->nullable();
            $table->string('sel_phone')->nullable();
            $table->string('sel_email')->nullable();
            $table->string('sel_att')->nullable();
            $table->string('inv_phone')->nullable()->change();
            $table->string('dev_phone')->nullable()->change();
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
