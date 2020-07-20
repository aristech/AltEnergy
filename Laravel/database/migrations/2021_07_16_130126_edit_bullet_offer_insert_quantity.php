<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditBulletOfferInsertQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bullet_offer', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bullet_offer', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
