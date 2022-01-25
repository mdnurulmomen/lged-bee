<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualPlanApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annual_plan_approvals', function (Blueprint $table) {
            $table->id();
            $table->integer('office_id');
            $table->integer('op_audit_calendar_event_id');
            $table->bigInteger('annual_plan_main_id');
            $table->bigInteger('activity_type');
            $table->string('status');
            $table->string('approval_status');
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
        Schema::dropIfExists('annual_plan_approvals');
    }
}
