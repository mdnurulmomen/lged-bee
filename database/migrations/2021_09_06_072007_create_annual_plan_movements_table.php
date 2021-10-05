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
            $table->bigInteger('sender_office_id');
            $table->string('sender_office_name_en');
            $table->string('sender_office_name_bn');
            $table->bigInteger('sender_unit_id');
            $table->string('sender_unit_name_en');
            $table->string('sender_unit_name_bn');
            $table->bigInteger('sender_officer_id');
            $table->string('sender_name_en');
            $table->string('sender_name_bn');
            $table->bigInteger('sender_designation_id');
            $table->string('sender_designation_en');
            $table->string('sender_designation_bn');
            $table->string('receiver_type', 20)->nullable();
            $table->bigInteger('receiver_office_id');
            $table->string('receiver_office_name_en');
            $table->string('receiver_office_name_bn');
            $table->bigInteger('receiver_unit_id');
            $table->string('receiver_unit_name_en');
            $table->string('receiver_unit_name_bn');
            $table->bigInteger('receiver_officer_id');
            $table->string('receiver_name_en');
            $table->string('receiver_name_bn');
            $table->bigInteger('receiver_designation_id');
            $table->string('receiver_designation_en');
            $table->string('receiver_designation_bn');
            $table->string('status', 16);
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
