<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->index(['office_req_id', 'approver_id']); // Adding indexes for performance
        });
    }

    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropIndex(['office_req_id', 'approver_id']);
        });
    }
};
