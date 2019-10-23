<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditClientsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients',function(Blueprint $table)
        {
            $table->string('plithos_diamerismaton')->nullable()->after('arithmos_aitisis');
            $table->string('dieuthinsi_paroxis')->nullable()->after('plithos_diamerismaton');
            $table->string('kw_oikiako')->nullable()->after('dieuthinsi_paroxis');
            $table->string('kw')->nullable()->after('kw_oikiako');
            $table->string('levitas')->nullable()->after('kw');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients',function(Blueprint $table)
        {
            $table->dropColumn('plithos_diamerismaton');
            $table->dropColumn('dieuthinsi_paroxis');
            $table->dropColumn('kw_oikiako');
            $table->dropColumn('kw');
            $table->dropColumn('levitas');
        });
    }
}
