<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Artel\Support\Traits\MigrationTrait;

class TwoFactorAuthEmailsCreateTable extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('two_factor_auth_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('code');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('two_factor_auth_emails');
    }
}
