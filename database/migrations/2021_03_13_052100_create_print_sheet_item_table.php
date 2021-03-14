<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the Print Sheet Items table.
 *
 * @package Database\Migrations
 */
class CreatePrintSheetItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_sheet_items', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('print_sheet_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('order_item_id')->constrained();
            $table->enum('status', ['pass','reject','complete'])->default('pass');
            $table->string('image_url', 255);
            $table->string('size', 255);
            $table->integer('x_pos');
            $table->integer('y_pos');
            $table->integer('width');
            $table->integer('height');
            $table->string('identifier', 255);
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
        Schema::drop('print_sheet_items');
    }
}
