<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('damage_type_id')->nullable()->unsigned();
            $table->foreign('damage_type_id')->references('id')->on('damage_types')->onDelete('set null');
            $table->text('damage_comments')->nullable();
            $table->float('cost',9,2)->default(0.00);
            $table->boolean('guarantee')->default(false);
            $table->string('status')->default('Μη ολοκληρωμένη');
            $table->boolean('damage_estimation')->default(false);
            $table->boolean('cost_information')->default(false);
            $table->boolean('supplement_available')->default(false);
            $table->boolean('fixing_appointment')->default(false);
            $table->boolean('damage_fixed')->default(false);
            $table->boolean('damage_paid')->default(false);
            $table->integer('client_id')->nullable()->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            //antallaktika
            $table->integer('manufacturer_id')->nullable()->unsigned();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('set null');
            $table->integer('mark_id')->nullable()->unsigned();
            $table->foreign('mark_id')->references('id')->on('marks')->onDelete('set null');
            $table->integer('device_id')->nullable()->unsigned();
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->string('supplement')->nullable();
            $table->text('comments')->nullable();
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('appointment_start')->nullable();
            $table->timestamp('appointment_end')->nullable();
            $table->boolean('repeatable')->default(false);
            $table->integer('repeat_frequency')->nullable();
            $table->string('repeat_type')->nullable();
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
        Schema::dropIfExists('damages');
    }
}
