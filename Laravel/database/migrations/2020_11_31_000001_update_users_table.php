<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users',function(Blueprint $table)
        {
            $table->string('telephone')->nullable()->after('email');
            $table->string('telephone2')->nullable()->after('telephone');
            $table->string('mobile')->nullable()->after('telephone2');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users',function(Blueprint $table)
        {
            $table->dropColumn('telephone');
            $table->dropColumn('telephone2');
            $table->dropColumn('mobile');
        });
    }
}
