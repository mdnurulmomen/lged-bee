<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApEntityIndividualAuditPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_entity_individual_audit_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('schedule_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('fiscal_year_id');
            $table->longText('plan_description');
            $table->bigInteger('draft_office_id');
            $table->bigInteger('draft_unit_id');
            $table->string('draft_unit_name_en');
            $table->string('draft_unit_name_bn');
            $table->bigInteger('draft_designation_id');
            $table->string('draft_designation_name_en');
            $table->string('draft_designation_name_bn');
            $table->bigInteger('draft_officer_id');
            $table->string('draft_officer_name_en');
            $table->string('draft_officer_name_bn');
            $table->string('status', 16);
            $table->bigInteger('created_by');
            $table->bigInteger('modified_by');
            $table->string('device_type', 50)->nullable();
            $table->string('device_id', 50)->nullable();
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
        Schema::connection('OfficeDB')->dropIfExists('ap_entity_individual_audit_plans');
    }
}
