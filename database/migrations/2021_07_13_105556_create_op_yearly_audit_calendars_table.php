<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_yearly_audit_calendars', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('employee_record_id');
            $table->string('initiator_name_en');
            $table->string('initiator_name_bn');
            $table->string('initiator_unit_name_en');
            $table->string('initiator_unit_name_bn');
            $table->string('status', 24);
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
        Schema::dropIfExists('op_yearly_audit_calendars');
    }
}
