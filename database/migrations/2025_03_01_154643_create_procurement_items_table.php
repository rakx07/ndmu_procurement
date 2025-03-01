<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementItemsTable extends Migration
{
    public function up()
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('item_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'purchased', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('procurement_requests')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('procurement_items');
    }
}
