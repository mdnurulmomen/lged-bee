<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApOfficeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_office_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('annual_plan_id');
            $table->bigInteger('schedule_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('duration_id');
            $table->bigInteger('outcome_id');
            $table->bigInteger('output_id');
            $table->string('memorandum_no',120);
            $table->date('memorandum_date');
            $table->text('heading_details');
            $table->text('advices');
            $table->string('approved_status',16);
            $table->text('order_cc_list');
            $table->longText('team_members');
            $table->longText('team_schedules');
            $table->bigInteger('draft_officer_id');
            $table->string('draft_officer_name_en');
            $table->string('draft_officer_name_bn');
            $table->bigInteger('draft_designation_id');
            $table->string('draft_designation_name_en');
            $table->string('draft_designation_name_bn');
            $table->bigInteger('created_by');
            $table->bigInteger('modified_by');
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
        Schema::dropIfExists('ap_office_orders');
    }
}
