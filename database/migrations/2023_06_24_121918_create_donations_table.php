<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->bigInteger('disaster_id');
            $table->string('name');
            $table->string('age');
            $table->string('contact_number');
            $table->string('email');
            $table->string('donation_type'); //if item or money or volunteer
            $table->string('donation_info'); //amount of money, type of goods, manpower or assistance
            $table->boolean('verified')->default(false);; 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
