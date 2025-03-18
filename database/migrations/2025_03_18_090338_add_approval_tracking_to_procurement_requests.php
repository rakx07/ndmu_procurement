<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->boolean('comptroller_approved')->default(false)->after('needs_admin_approval');
        });
    }

    public function down()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->dropColumn('comptroller_approved');
        });
    }
};
