<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApRiskAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('office_order_id');
            $table->string('risk_assessment_type');
            $table->double('total_risk_value',8,2);
            $table->double('risk_rate',8,2);
            $table->string('risk');
            $table->bigInteger('created_by');
            $table->string('created_by_name_en');
            $table->string('created_by_name_bn');
            $table->bigInteger('updated_by')->nullable();
            $table->string('updated_by_name_en')->nullable();
            $table->string('updated_by_name_bn')->nullable();
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
        Schema::dropIfExists('ap_risk_assessments');
    }
}
