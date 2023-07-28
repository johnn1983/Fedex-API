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
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('prod_id', 'product_id');
            $table->renameColumn('prod_width', 'product_width');
            $table->renameColumn('prod_height', 'product_height');
            $table->renameColumn('prod_depth', 'product_depth');
            $table->renameColumn('prod_artist', 'product_artist');
            $table->renameColumn('prod_title', 'product_title');
            $table->renameColumn('prod_weight', 'product_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
};
