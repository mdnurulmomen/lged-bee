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
        Schema::create('ap_organization_yearly_plan_budgets', function (Blueprint $table) {
            $table->id();
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
