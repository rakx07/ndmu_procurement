<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('id');
        });
    }
    
    public function down()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });
    }
};
