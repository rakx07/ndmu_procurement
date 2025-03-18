<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            if (!Schema::hasColumn('procurement_items', 'office_id')) {
                $table->unsignedBigInteger('office_id')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            if (Schema::hasColumn('procurement_items', 'office_id')) {
                $table->dropForeign(['office_id']);
$table->dropColumn('office_id');
            }
        });
    }
};
