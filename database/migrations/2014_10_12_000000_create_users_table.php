<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('age');
            $table->string('address');
            $table->string('user_type');
            $table->string('open_close_time')->nullable(); //for repair shop
            $table->string('open_close_date')->nullable(); //for repair shop
            $table->string('landmark')->nullable(); //for repair shop
            $table->string('filename')->nullable(); //for repair shop
            $table->string('path')->nullable(); //for repair shop
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
