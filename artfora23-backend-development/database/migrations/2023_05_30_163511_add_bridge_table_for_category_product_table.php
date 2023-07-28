<?php

use Illuminate\Database\Migrations\Migration;
use Artel\Support\Traits\MigrationTrait;

return new class extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createBridgeTable('Product', 'Category');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropBridgeTable('Product', 'Category');
    }
};
