<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('tagname');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->integer('role_id');
            $table->string('reset_password_hash')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_token_sent_at')->nullable();
            $table->text('description')->default('');
            $table->string('country')->default('USA');
            $table->string('external_link')->nullable();
            $table->jsonb('data')->default(json_encode([
                'media_filters' => [
                    'page' => 1,
                    'per_page' => 10
                ]
            ]));
            $table->unsignedInteger('background_image_id')->nullable();
            $table->unsignedInteger('avatar_image_id')->nullable();

            $table->enum('2fa_type', ['email', 'sms', 'otp'])->default('email');
            $table->string('otp_secret')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();

            $table
                ->foreign('background_image_id')
                ->references('id')
                ->on('media')
                ->onDelete('set null');

            $table
                ->foreign('avatar_image_id')
                ->references('id')
                ->on('media')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
