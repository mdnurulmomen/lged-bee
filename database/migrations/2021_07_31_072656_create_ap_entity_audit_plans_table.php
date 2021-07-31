<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApEntityAuditPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_entity_audit_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('ap_organization_yearly_plan_rp_id');
            $table->integer('party_id');
            $table->longText('plan_description');
            $table->integer('draft_office_id');
            $table->integer('draft_unit_id');
            $table->string('draft_unit_name_en');
            $table->string('draft_unit_name_bn');
            $table->integer('draft_designation_id');
            $table->string('draft_designation_name_en');
            $table->string('draft_designation_name_bn');
            $table->integer('draft_officer_id');
            $table->string('draft_officer_name_en');
            $table->string('draft_officer_name_bn');
            $table->integer('created_by');
            $table->integer('modified_by');
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
        Schema::dropIfExists('ap_entity_audit_plans');
    }
}
