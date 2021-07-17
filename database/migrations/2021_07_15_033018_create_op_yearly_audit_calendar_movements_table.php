<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_yearly_audit_calendar_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('op_yearly_calendar_id');
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('office_id');
            $table->integer('unit_id');
            $table->string('unit_name_en');
            $table->string('unit_name_bn');
            $table->string('officer_type');
            $table->integer('employee_id');
            $table->integer('employee_designation_id');
            $table->string('employee_designation_en');
            $table->string('employee_designation_bn');
            $table->integer('user_id');
            $table->string('calendar_status');
            $table->integer('received_by')->nullable();
            $table->integer('sent_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
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
        Schema::dropIfExists('op_yearly_audit_calendar_approval_movements');
    }
}
