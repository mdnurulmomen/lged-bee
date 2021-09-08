<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApEntityIndividualAuditPlanMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_entity_individual_audit_plan_movements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('schedule_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('fiscal_year_id');
            $table->integer('office_id');
            $table->integer('unit_id');
            $table->string('unit_name_en');
            $table->string('unit_name_bn');
            $table->string('officer_type');
            $table->integer('employee_id');
            $table->integer('employee_designation_id');
            $table->string('employee_designation_en');
            $table->string('employee_designation_bn');
            $table->integer('user_id');
            $table->string('audit_plan_status');
            $table->integer('received_by')->nullable();
            $table->integer('sent_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
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
        Schema::connection('OfficeDB')->dropIfExists('ap_entity_individual_audit_plan_movements');
    }
}
