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
        Schema::table('products', function (Blueprint $table) {
            $table->float('width')->default(0);
            $table->float('height')->default(0);
            $table->float('depth')->default(0);
            $table->float('price_in_euro')->default(0);
            $table->float('shipping_in_euro')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->dropColumn('depth');
            $table->dropColumn('price_in_euro');
            $table->dropColumn('shipping_in_euro');
        });
    }
};
