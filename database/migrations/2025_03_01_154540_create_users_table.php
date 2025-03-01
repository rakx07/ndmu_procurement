<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('role')->default(0); // 0: Staff, 1: Purchasing Officer, 2: Supervisor, 3: Admin, 4: Comptroller, 5: IT Admin
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
