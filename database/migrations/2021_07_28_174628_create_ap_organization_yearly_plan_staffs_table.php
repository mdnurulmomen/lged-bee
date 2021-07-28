<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApOrganizationYearlyPlanStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ap_organization_yearly_plan_staffs', function (Blueprint $table) {
            $table->id();
            $table->integer('schedule_id');
            $table->integer('activity_id');
            $table->integer('milestone_id');
            $table->integer('employee_id');
            $table->integer('office_id');
            $table->integer('unit_id');
            $table->string('unit_name_en');
            $table->string('unit_name_bn');
            $table->integer('designation_id');
            $table->string('employee_name_en');
            $table->string('employee_name_bn');
            $table->string('employee_designation_en');
            $table->string('employee_designation_bn');
            $table->tinyInteger('employee_grade');
            $table->string('employee_category');
            $table->date('task_start_date_plan');
            $table->date('task_end_date_plan');
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
        Schema::dropIfExists('ap_organization_yearly_plan_staffs');
    }
}
