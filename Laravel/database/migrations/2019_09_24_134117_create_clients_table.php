<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('afm');
            $table->string('doy');
            $table->string('telephone')->nullable();
            $table->string('telephone2')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address');
            $table->string('zipcode');
            $table->string('location');
            $table->string('level')->nullable();
            $table->integer('manager_id')->nullable()->unsigned();
            $table->foreign('manager_id')->references('id')->on('managers')->onDelete('set null');
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
        Schema::dropIfExists('clients');
    }
}
