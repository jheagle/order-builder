<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->default('')->index();
            $table->string('vendor', 50)->nullable()->index();
            $table->string('type', 25)->nullable()->index();
            $table->string('size', 20)->nullable()->index();
            $table->float('price')->default(0);
            $table->string('handle', 75)->nullable();
            $table->integer('inventory_quantity')->default(0);
            $table->string('sku', 30)->nullable()->index();
            $table->string('design_url', 255)->nullable();
            $table->enum('published_state', ['inactive','active'])->default('active')->index();
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
        Schema::drop('products');
    }
}
