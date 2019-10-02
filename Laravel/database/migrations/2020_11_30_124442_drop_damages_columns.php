<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDamagesColumns extends Migration
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
            $table->dropColumn('damage_fixed');
            $table->dropColumn('damage_estimation');
            $table->dropColumn('cost_information');
            $table->dropColumn('supplement_available');
            $table->dropColumn('fixing_appointment');
            $table->dropColumn('damage_paid');
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
            $table->boolean('damage_estimation')->default(false)->after('status');
            $table->boolean('cost_information')->default(false)->after('damage_estimation');
            $table->boolean('supplement_available')->default(false)->after('cost_information');
            $table->boolean('fixing_appointment')->default(false)->after('supplement_available');
            $table->boolean('damage_fixed')->default(false)->after('fixing_appointment');
            $table->boolean('damage_paid')->default(false)->after('damage_fixed');
        });
    }
}

