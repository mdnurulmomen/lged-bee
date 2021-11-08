<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXRiskAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('risk_assessment_type');
            $table->string('company_type');
            $table->string('risk_assessment_title_bn');
            $table->string('risk_assessment_title_en');
            $table->bigInteger('created_by_id');
            $table->bigInteger('updated_by_id');
            $table->string('created_by_name');
            $table->string('updated_by_name');
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
        Schema::dropIfExists('x_risk_assessments');
    }
}
