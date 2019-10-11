<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_type_id')->nullable()->unsigned();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('set null');
            $table->text('service_comments')->nullable();
            $table->float('cost',9,2)->default(0.00);
            $table->boolean('guarantee')->default(false);
            $table->string('status')->default('Μη ολοκληρωμένη');
            $table->boolean('appointment_pending')->default(false);
            $table->boolean('technician_left')->default(false);
            $table->boolean('technician_arrived')->default(false);
            $table->boolean('appointment_completed')->default(false);
            $table->boolean('appointment_needed')->default(false);
            $table->boolean('supplement_pending')->default(false);
            $table->boolean('service_completed')->default(false);
            $table->boolean('completed_no_transaction')->default(false);
            $table->integer('client_id')->nullable()->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            //antallaktika
            $table->integer('manufacturer_id')->nullable()->unsigned();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('set null');
            $table->integer('mark_id')->nullable()->unsigned();
            $table->foreign('mark_id')->references('id')->on('marks')->onDelete('set null');
            $table->integer('device_id')->nullable()->unsigned();
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->string('supplements')->nullable();
            $table->text('comments')->nullable();
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('appointment_start')->nullable();
            $table->timestamp('appointment_end')->nullable();
            $table->boolean('repeatable')->default(false);
            $table->string('frequency')->nullable();
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
        Schema::dropIfExists('services');
    }
}
