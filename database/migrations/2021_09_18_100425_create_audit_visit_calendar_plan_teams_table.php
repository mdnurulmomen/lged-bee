<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditVisitCalendarPlanTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_visit_calendar_plan_teams', function (Blueprint $table) {
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
            $table->bigInteger('entity_id');
            $table->string('entity_name_en');
            $table->string('entity_name_bn');
            $table->string('team_name');
            $table->date('team_start_date');
            $table->date('team_end_date');
            $table->json('team_members');
            $table->string('leader_name_en');
            $table->string('leader_name_bn');
            $table->bigInteger('leader_designation_id');
            $table->string('leader_designation_name_en');
            $table->string('leader_designation_name_bn');
            $table->bigInteger('team_parent_id');
            $table->tinyInteger('activity_man_days');
            $table->year('audit_year_start');
            $table->year('audit_year_end');
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
        Schema::dropIfExists('audit_visit_calendar_plan_teams');
    }
}
