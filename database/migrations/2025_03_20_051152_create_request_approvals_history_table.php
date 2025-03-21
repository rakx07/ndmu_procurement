<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('request_approvals_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_req_id'); // References Procurement Request
            $table->unsignedBigInteger('approver_id'); // Who approved/rejected
            $table->integer('role'); // 2 = Supervisor, 3 = Admin, 4 = Comptroller
            $table->string('status'); // approved, rejected
            $table->text('remarks')->nullable();
            $table->timestamps(); // When action was taken

            $table->foreign('office_req_id')->references('id')->on('procurement_requests')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_approvals_history');
    }
};

