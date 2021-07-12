<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpYearlyAuditCalendarResponsiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_yearly_audit_calendar_responsibles', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->integer('activity_id');
            $table->integer('op_yearly_audit_calendar_id');
            $table->integer('office_id');
            $table->integer('office_type');
            $table->string('short_name_en');
            $table->string('short_name_bn');
            $table->string('office_name_en');
            $table->string('office_name_bn');
            $table->text('remarks');
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
        Schema::dropIfExists('op_yearly_audit_calendar_responsibles');
    }
}
