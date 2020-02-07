<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreeAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appointment_title')->nullable();
            $table->text('appointment_description')->nullable();
            $table->boolean('appointment_completed');
            $table->string('appointment_location')->nullable();
            $table->string('appointment_start')->nullable();
            $table->string('appointment_end')->nullable();
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
        Schema::dropIfExists('free_appointments');
    }
}
