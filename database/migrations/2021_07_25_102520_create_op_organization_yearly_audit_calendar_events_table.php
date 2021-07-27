<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpOrganizationYearlyAuditCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_organization_yearly_audit_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->integer('office_id');
            $table->integer('op_yearly_audit_calendar_id');
            $table->json('audit_calendar_data');
            $table->integer('activity_count');
            $table->integer('milestone_count');
            $table->string('status', 10);
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
        Schema::dropIfExists('op_organization_yearly_audit_calendar_events');
    }
}
