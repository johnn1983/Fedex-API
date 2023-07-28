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
        Schema::create('seller_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->string('subscription_id');
            $table->string('price_id');
            $table->enum('stripe_status', ['successed', 'pending', 'failed', 'canceled', 'expired'])
            ->default('pending');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
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
        Schema::dropIfExists('seller_subscriptions');
    }
};
