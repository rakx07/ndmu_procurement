<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeIdToProcurementItemsTable extends Migration
{
    public function up()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->after('status')->nullable();

            // If you have an `offices` table, you can add a foreign key constraint:
            // $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropColumn('office_id');
        });
    }
}
