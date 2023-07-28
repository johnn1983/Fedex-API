<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Artel\Support\Traits\MigrationTrait;
use Illuminate\Support\Facades\DB;

class ProductsCreateTable extends Migration
{
    use MigrationTrait;

    public function up()
    {
        $this->createTable();

        $this->createBridgeTable('Product', 'Media');

        $this->addForeignKey('Product', 'User');

        $this->addForeignKey('Product', 'Category');
    }

    public function down()
    {
        $this->dropBridgeTable('Product', 'Media');

        Schema::dropIfExists('products');
    }

    public function createTable()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('price');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('slug');
            $table->string('tags');
            $table->text('description');
            $table->boolean('is_ai_safe')->default(false);
            $table->unsignedInteger('visibility_level')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->float('weight')->nullable();
            $table->enum('status', [ 'Approved', 'Rejected', 'Pending' ])->default('Pending');

            $table->softDeletes();
            $table->timestamps();
        });
    }
}
