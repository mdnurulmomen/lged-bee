<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditVisitCalenderPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_visit_calender_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('duration_id');
            $table->bigInteger('outcome_id');
            $table->bigInteger('output_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('ministry_id');
            $table->bigInteger('cost_center_id');
            $table->string('cost_center_name_en');
            $table->string('cost_center_name_bn');
            $table->bigInteger('team_id');
            $table->date('team_start_date');
            $table->date('team_end_date');
            $table->date('team_member_start_date');
            $table->date('team_member_end_date');
            $table->string('team_member_name_en');
            $table->string('team_member_name_bn');
            $table->string('team_member_designation_en');
            $table->string('team_member_designation_bn');
            $table->string('team_member_role_en');
            $table->string('team_member_role_bn');
            $table->string('team_member_activity');
            $table->string('team_member_activity_description');
            $table->string('activity_location');
            $table->string('activity_man_days');
            $table->string('mobile_no');
            $table->string('fiscal_year');
            $table->string('approve_status', 16);
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
        Schema::dropIfExists('audit_visit_calender_plans');
    }
}
