<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarEditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_yearly_audit_calendar_edit_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('op_yearly_calendar_id');
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('unit_id');
            $table->integer('employee_id');
            $table->integer('user_id');
            $table->integer('employee_designation_id');
            $table->json('old_data');
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
        Schema::dropIfExists('op_yearly_audit_calendar_edit_histories');
    }
}
