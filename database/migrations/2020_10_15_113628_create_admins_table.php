<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->enum('status',['active','blocked'])->default('active');
            $table->enum('level',['support','subadmin','administrator']);
            $table->timestamps();
        });

        DB::table('admins')->insert([
            'name' => 'Administrador',
            'email' => 'admin',
            'password' => '$2y$10$kK6qMJFIpwemy0hnmqViDOKAEMoqEh78GsLhP0hRvLeZuk.7BAiQO',
            'level' => 'administrator',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
