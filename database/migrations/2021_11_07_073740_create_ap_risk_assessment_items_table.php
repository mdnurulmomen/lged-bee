<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApRiskAssessmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_risk_assessment_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ap_risk_assessment_id');
            $table->bigInteger('x_risk_assessment_id');
            $table->string('risk_assessment_title_bn');
            $table->string('risk_assessment_title_en');
            $table->tinyInteger('yes');
            $table->tinyInteger('no');
            $table->tinyInteger('risk_value');
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
        Schema::dropIfExists('ap_risk_assessment_items');
    }
}
