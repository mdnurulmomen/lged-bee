<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualPlanMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('BeeCoreDB')->create('annual_plan_movements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('op_audit_calendar_event_id');
            $table->bigInteger('duration_id');
            $table->bigInteger('outcome_id');
            $table->bigInteger('output_id');
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('office_id');
            $table->string('office_name_en');
            $table->string('office_name_bn');
            $table->bigInteger('unit_id');
            $table->string('unit_name_en');
            $table->string('unit_name_bn');
            $table->integer('receiver_id');
            $table->string('receiver_type',20)->nullable();
            $table->string('receiver_name_en');
            $table->string('receiver_name_bn');
            $table->integer('receiver_designation_id');
            $table->string('receiver_designation_en');
            $table->string('receiver_designation_bn');
            $table->integer('sender_id');
            $table->string('sender_name_en');
            $table->string('sender_name_bn');
            $table->integer('sender_designation_id');
            $table->string('sender_designation_en');
            $table->string('sender_designation_bn');
            $table->string('status',16);
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('annual_plan_movements');
    }
}
