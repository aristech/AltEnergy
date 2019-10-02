<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('damages', function (Blueprint $table)
        {
            // $table->dropColumn('damage_fixed');
            // $table->dropColumn('damage_estimation');
            // $table->dropColumn('cost_information');
            // $table->dropColumn('supplement_available');
            // $table->dropColumn('fixing_appointment');
            // $table->dropColumn('damage_paid');
            $table->boolean('appointment_pending')->default(false)->after('status');
            $table->boolean('technician_left')->default(false)->after('appointment_pending');
            $table->boolean('technician_arrived')->default(false)->after('technician_left');
            $table->boolean('appointment_completed')->default(false)->after('technician_arrived');
            $table->boolean('appointment_needed')->default(false)->after('appointment_completed');
            $table->boolean('supplement_pending')->default(false)->after('appointment_needed');
            $table->boolean('damage_fixed')->default(false)->after('supplement_pending');
            $table->boolean('completed_no_transaction')->default(false)->after('damage_fixed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('damages', function (Blueprint $table)
        {
            $table->dropColumn('appointment_pending');
            $table->dropColumn('technician_left');
            $table->dropColumn('technician_arrived');
            $table->dropColumn('appointment_completed');
            $table->dropColumn('appointment_needed');
            $table->dropColumn('supplement_pending');
            $table->dropColumn('damage_fixed');
            $table->dropColumn('completed_no_transaction');
            // $table->boolean('damage_estimation')->default(false)->after('status');
            // $table->boolean('cost_information')->default(false)->after('damage_estimation');
            // $table->boolean('supplement_available')->default(false)->after('cost_information');
            // $table->boolean('fixing_appointment')->default(false)->after('supplement_available');
            // $table->boolean('damage_fixed')->default(false)->after('fixing_appointment');
            // $table->boolean('damage_paid')->default(false)->after('damage_fixed');
        });


    }
}
