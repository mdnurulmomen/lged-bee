<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditVisitCalenderPlanMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_visit_calender_plan_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id');
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('duration_id');
            $table->bigInteger('outcome_id');
            $table->bigInteger('output_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('ministry_id');
            $table->bigInteger('entity_id');
            $table->bigInteger('cost_center_id');
            $table->string('cost_center_name_en');
            $table->string('cost_center_name_bn');
            $table->string('team_member_name_en')->nullable();
            $table->string('team_member_name_bn')->nullable();
            $table->bigInteger('team_member_designation_id')->default(0);
            $table->string('team_member_designation_en')->nullable();
            $table->string('team_member_designation_bn')->nullable();
            $table->string('team_member_role_en')->nullable();
            $table->string('team_member_role_bn')->nullable();
            $table->date('team_member_start_date');
            $table->date('team_member_end_date');
            $table->string('team_member_activity')->nullable();
            $table->string('team_member_activity_description')->nullable();
            $table->string('activity_location')->nullable();
            $table->string('comment')->nullable();
            $table->string('mobile_no');
            $table->string('approve_status', 16)->default('approved');
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
        Schema::dropIfExists('audit_visit_calender_plan_members');
    }
}
