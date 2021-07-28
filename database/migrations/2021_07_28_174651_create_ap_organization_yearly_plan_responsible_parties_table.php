<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApOrganizationYearlyPlanResponsiblePartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ap_organization_yearly_plan_responsible_parties', function (Blueprint $table) {
            $table->id();
            $table->integer('schedule_id');
            $table->integer('activity_id');
            $table->integer('milestone_id');
            $table->integer('party_id');
            $table->string('party_name_en');
            $table->string('party_name_bn');
            $table->string('party_type');
            $table->integer('ministry_id');
            $table->string('ministry_name_en');
            $table->string('ministry_name_bn');
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
        Schema::dropIfExists('ap_organization_yearly_plan_responsible_parties');
    }
}
