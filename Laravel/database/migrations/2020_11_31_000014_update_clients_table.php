<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function($table)
        {
            $table->string('foldername')->after('levitas');
            $table->string('doy')->nullable()->change();
            $table->string('afm')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function($table)
        {
            $table->dropColumn('foldername');
            $table->string('doy')->change();
            $table->string('afm')->change();

        });
    }
}
