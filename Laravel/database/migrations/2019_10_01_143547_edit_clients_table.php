<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditClientsTable extends Migration
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
            $table->string('arithmos_gnostopoihshs')->nullable()->after('doy');
            $table->string('arithmos_meletis')->nullable()->after('arithmos_gnostopoihshs');
            $table->string('arithmos_hkasp')->nullable()->after('arithmos_meletis');
            $table->string('arithmos_aitisis')->nullable()->after('arithmos_hkasp');



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
            $table->string('arithmos_gnostopoihshs');
            $table->string('arithmos_meletis');
            $table->string('arithmos_hkasp');
            $table->string('arithmos_aitisis');
        });
    }
}
