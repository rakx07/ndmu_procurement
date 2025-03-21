<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalsTable extends Migration
{
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_req_id');
            $table->unsignedBigInteger('approver_id');
            $table->integer('role'); // Keep as integer (0 = Staff, 2 = Supervisor, etc.)

            // Updated status ENUM to support multiple approval levels
            $table->enum('status', [
                'pending',
                'supervisor_approved',
                'admin_approved',
                'comptroller_approved',
                'approved', // Final approval status
                'rejected'
            ])->default('pending');

            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('office_req_id')->references('id')->on('procurement_requests')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvals');
    }
}
