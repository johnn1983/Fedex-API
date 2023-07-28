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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('prod_title');
            $table->string('prod_artist');
            $table->float('prod_width')->default(0);
            $table->float('prod_height')->default(0);
            $table->float('prod_weight')->default(0);
            $table->float('prod_depth')->default(0);
            $table->float('price')->default(0);
            $table->integer('quantity')->default(1);
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
        Schema::dropIfExists('order__items');
    }
};
