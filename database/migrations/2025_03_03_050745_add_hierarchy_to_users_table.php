<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHierarchyToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('role');
            $table->unsignedBigInteger('administrator_id')->nullable()->after('supervisor_id');

            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('administrator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropForeign(['administrator_id']);
            $table->dropColumn(['supervisor_id', 'administrator_id']);
        });
    }
}
