<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ac_queries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fiscal_year_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('audit_plan_id');
            $table->bigInteger('office_order_id');
            $table->bigInteger('team_id');
            $table->bigInteger('cost_center_type_id');
            $table->bigInteger('ministry_id');
            $table->bigInteger('controlling_office_id');
            $table->string('controlling_office_name_en');
            $table->string('controlling_office_name_bn');
            $table->bigInteger('entity_office_id');
            $table->string('entity_office_name_en');
            $table->string('entity_office_name_bn');
            $table->bigInteger('cost_center_id');
            $table->string('cost_center_name_en');
            $table->string('cost_center_name_bn');
            $table->bigInteger('query_id');
            $table->string('query_title_en');
            $table->string('query_title_bn');
            $table->boolean('is_query_sent')->default(0);
            $table->date('query_send_date')->nullable();
            $table->boolean('is_query_document_received')->default(0);
            $table->date('query_document_received_date')->nullable();
            $table->bigInteger('querier_officer_id');
            $table->bigInteger('querier_designation_id');
            $table->bigInteger('query_receiver_officer_id')->nullable();
            $table->bigInteger('query_receiver_designation_id')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('has_memo')->default(0);
            $table->string('status')->nullable();
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
        Schema::dropIfExists('ac_queries');
    }
}
