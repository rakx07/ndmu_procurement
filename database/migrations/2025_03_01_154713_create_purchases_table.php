<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('purchasing_officer_id');
            $table->string('supplier_name');
            $table->string('supplier_contact');
            $table->date('purchased_date');
            $table->decimal('total_amount', 10, 2);
            $table->enum('purchase_status', ['in-progress', 'completed', 'cancelled'])->default('in-progress');
            $table->string('invoice_file')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('procurement_requests')->onDelete('cascade');
            $table->foreign('purchasing_officer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
