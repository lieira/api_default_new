<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) { 
            $table->increments('id')->unique(); //ID INCREMENTAL E UNICO
            $table->enum('status',['pending','sended','aproved','blocked'])->default('pending'); //ESTADO DA DOCUMENTACAO DO USUARIO
            $table->string('name'); // NOME DO USUARIO
            $table->string('email')->unique();// EMAIL
            $table->enum('email_status',['confirmed','unconfirmed'])->default('unconfirmed'); //STATUS DO EMAIL
            $table->string('password')->nullable(); // SENHA DO USUARIO
            $table->string('birth')->nullable(); //DATA DE NASCIMENTO
            $table->enum('sex',['male','female'])->nullable(); //SEXO DO USUARIO
            $table->string('phone')->nullable(); //TELEFONE DO USUARIO
            $table->string('address_cep')->nullable(); //CEP DO USUARIO
            $table->string('address_street')->nullable(); //RUA DO USUARIO
            $table->string('address_number')->nullable(); //NUMERO DE ENDERECO DO USUARIO
            $table->string('address_complement')->nullable(); //COMPLEMENTO SE TIVER
            $table->string('address_district')->nullable(); //BAIRRO DO USUARIO
            $table->string('address_city')->nullable(); //CIDADE DO USUARIO
            $table->string('address_state')->nullable(); //ESTADO DO USUARIO
            $table->enum('first_login',['yes','no'])->default('yes'); //VERIFICACAO DE PRIMEIRA VEZ LOGANDO
            $table->string('note')->nullable(); //observacao usuario
            $table->string('email_code')->nullable(); //CODIGO DE EMAIL
            $table->string('password_code')->unique()->nullable(); //CODIGO DE RECUPERACAO DE SENHA
            $table->string('provider'); //LARAVEL SOCIALITE - NOME
            $table->string('provider_id'); //LARAVEL SOCIALITE - ID
            $table->string('profile_image')->nullable(); //FOTO DE PERFIL DO USUARIO
            $table->string('path_document_front')->nullable(); //COMPROVANTE DE DOCUMENTO - FRENTE
            $table->string('path_document_back')->nullable(); //COMPROVANTE DE DOCUMENTO - VERSO
            $table->string('path_document_address')->nullable(); //COMPROVANTE DE DOCUMENTO - ENDERECO
            $table->dateTime('last_login')->nullable(); //ULTIMA VEZ LOGADO
            $table->timestamps();
        });

        DB::table('users')->insert([
            'name' => 'Leonardo Vieira Peres',
            'email' => 'vieirapleo@gmail.com',
            'password' => '$2y$10$kK6qMJFIpwemy0hnmqViDOKAEMoqEh78GsLhP0hRvLeZuk.7BAiQO',
        ]);        
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
}
