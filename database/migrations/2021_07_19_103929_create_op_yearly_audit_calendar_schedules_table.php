<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_organization_yearly_audit_calendar_event_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->integer('activity_id');
            $table->string('activity_title_en');
            $table->string('activity_title_bn');
            $table->integer('activity_responsible_id');
            $table->integer('activity_milestone_id');
            $table->integer('op_yearly_audit_calendar_activity_id');
            $table->integer('op_yearly_audit_calendar_id');
            $table->string('milestone_title_en');
            $table->string('milestone_title_bn');
            $table->date('milestone_target');
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
        Schema::dropIfExists('op_organization_yearly_audit_calendar_event_schedules');
    }
}
