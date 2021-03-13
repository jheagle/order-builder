<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->float('total_price')->default(0);
            $table->string('fulfillment_status', 25)->index();
            $table->timestamp('fulfilled_date');
            $table->enum('order_status', ['pending','active','done','cancelled','resend']);
            $table->integer('customer_order_count');
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
