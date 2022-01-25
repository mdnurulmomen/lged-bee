<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualPlanMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('annual_plan_main', function (Blueprint $table) {
            $table->id();
            $table->integer('op_audit_calendar_event_id');
            $table->integer('fiscal_year_id');
            $table->integer('activity_id')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('status')->nullable();
            $table->string('approval_status')->nullable();
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
        Schema::dropIfExists('annual_plan_mains');
    }
}
