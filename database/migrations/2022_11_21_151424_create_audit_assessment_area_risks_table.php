<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditAssessmentAreaRisksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_assessment_area_risks', function (Blueprint $table) {
            $table->id();
            $table->string('inherent_risk');
            $table->unsignedInteger('x_risk_assessment_impact_id');
            $table->unsignedInteger('x_risk_assessment_likelihood_id');
            $table->string('control_system');
            $table->string('control_effectiveness');
            $table->string('residual_risk');
            $table->string('recommendation');
            $table->string('implemented_by');
            $table->string('implementation_period');
            $table->unsignedInteger('risk_assessment_area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_assessment_area_risks');
    }
}
