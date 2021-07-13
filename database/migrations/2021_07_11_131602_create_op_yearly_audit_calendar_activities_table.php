<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_yearly_audit_calendar_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->integer('activity_id');
            $table->integer('milestone_id');
            $table->date('target_date')->nullable();
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
        Schema::dropIfExists('op_yearly_audit_calendar_activities');
    }
}
