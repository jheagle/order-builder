<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the Orders table.
 *
 * @package Database\Migrations
 */
class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('order_number')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->default(null)->index();
            $table->float('total_price')->default(0);
            $table->string('fulfillment_status', 25)->nullable()->default(null)->index();
            $table->timestamp('fulfilled_date')->nullable()->default(null);
            $table->enum('order_status', ['pending','active','done','cancelled','resend'])->default(null);
            $table->integer('customer_order_count')->nullable()->default(null);
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
        Schema::drop('orders');
    }
}
