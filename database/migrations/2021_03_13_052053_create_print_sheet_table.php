<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the Print Sheets table.
 *
 * @package Database\Migrations
 */
class CreatePrintSheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_sheets', function (Blueprint $table) {
            $table->id()->index();
            $table->enum('type', ['ecom','test'])->default('ecom');
            $table->string('sheet_url', 255);
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
        Schema::drop('print_sheets');
    }
}
