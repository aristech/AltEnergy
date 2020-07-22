<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('title_id')->nullable();
            $table->string('status')->default('Μη Ολοκληρωμένο');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->string('marks')->nullable();
            $table->string('techs')->nullable();
            $table->string('appointment_start')->nullable();
            $table->string('appointment_end')->nullable();
            $table->boolean('aitisi_eda')->default(false);
            $table->boolean('aitisi_paroxou')->default(false);
            $table->boolean('upografi_aitisis')->default(false);
            $table->boolean('parallagi_sxedion')->default(false);
            $table->boolean('rantevou_xaraksis_metriti')->default(false);
            $table->boolean('topothetisi_metriti')->default(false);
            $table->boolean('katathesi_meletis')->default(false);
            $table->boolean('egkrisi_meletis')->default(false);
            $table->boolean('katathesi_pistopoihtikon')->default(false);
            $table->boolean('udrauliki_egkatastasi')->default(false);
            $table->boolean('kleisimo_grammis_aeriou')->default(false);
            $table->boolean('dokimi_steganotitas')->default(false);
            $table->boolean('rantevou_elegxou')->default(false);
            $table->boolean('rantevou_epanelegxou')->default(false);
            $table->boolean('enausi')->default(false);
            $table->boolean('ekdosi_fullou_kausis')->default(false);
            $table->boolean('timologio')->default(false);
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
        Schema::dropIfExists('projects');
    }
}
