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
            $table->string('damage_type');
            $table->text('damage_comments')->nullable();
            $table->float('cost',9,2)->default(0.00);
            $table->boolean('guarantee')->default(false);
            $table->string('status')->default('Μη ολοκληρωμένη');
            $table->boolean('estimation_appointment')->default(false);
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
