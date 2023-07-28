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
        Schema::create('seller_payout_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('order_id');
            $table->float('total_pay_amount');
            $table->date('order_date')->default(now());
            $table->enum('pay_status', ['successed', 'pending', 'failed', 'canceled'])
            ->default('pending');
            $table->string('pay_transaction_id');

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
        Schema::dropIfExists('seller_payout_histories');
    }
};
