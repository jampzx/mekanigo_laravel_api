<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToDisasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->boolean('active')->default(true); // Example: Adding a nullable string column

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}
