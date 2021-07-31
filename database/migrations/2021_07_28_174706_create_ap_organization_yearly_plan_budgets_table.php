<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApOrganizationYearlyPlanBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_organization_yearly_plan_budgets', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->integer('op_yearly_audit_calendar_id');
            $table->integer('op_yearly_audit_calendar_activity_id');
            $table->integer('schedule_id');
            $table->integer('activity_id');
            $table->integer('milestone_id');
            $table->integer('budget');
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
        Schema::dropIfExists('ap_organization_yearly_plan_budgets');
    }
}
