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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->float('vat')->default(0);
            $table->float('currency')->default(0);
            $table->float('shipping')->default(0);
            $table->string('inv_address');
            $table->string('inv_address2');
            $table->string('inv_zip');
            $table->string('inv_city');
            $table->string('inv_state');
            $table->string('inv_country');
            $table->integer('inv_phone');
            $table->string('inv_email');
            $table->string('inv_att');
            $table->string('dev_address');
            $table->string('dev_address2');
            $table->string('dev_zip');
            $table->string('dev_city');
            $table->string('dev_state');
            $table->string('dev_country');
            $table->integer('dev_phone');
            $table->string('dev_email');
            $table->string('dev_att');
            $table->enum('order_status', ['paid', 'checkout', 'canceled', 'failed', 'expired'])
            ->default('checkout');
            $table->string('transaction_id');
            $table->string('payment_mode');
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
