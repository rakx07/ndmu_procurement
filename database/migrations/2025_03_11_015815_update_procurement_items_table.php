<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->string('supplier_name')->nullable();
            $table->unsignedBigInteger('item_category_id')->nullable();
            
            $table->foreign('item_category_id')->references('id')->on('item_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropForeign(['item_category_id']);
            $table->dropColumn('supplier_name');
            $table->dropColumn('item_category_id');
        });
    }
};
