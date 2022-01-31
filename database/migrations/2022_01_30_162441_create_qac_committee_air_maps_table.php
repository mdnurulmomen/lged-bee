<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQacCommitteeAirMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('qac_committee_air_maps', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year_id');
            $table->string('qac_type');
            $table->bigInteger('qac_committee_id');
            $table->bigInteger('air_report_id');
            $table->bigInteger('created_by');
            $table->string('created_by_bn');
            $table->string('created_by_en');
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
        Schema::dropIfExists('qac_committee_air_maps');
    }
}
