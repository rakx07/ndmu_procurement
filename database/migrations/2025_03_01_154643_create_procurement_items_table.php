<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->nullable(); // ✅ Make `request_id` nullable
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('available');
            $table->unsignedBigInteger('office_id')->nullable();
            $table->timestamps();

            // ✅ Keep Foreign Keys (Ensure `onDelete('cascade')` works properly)
            $table->foreign('request_id')->references('id')->on('procurement_requests')->onDelete('cascade');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_items');
    }
};
