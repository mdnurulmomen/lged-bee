<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApottiItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('apotti_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('apotti_id');
            $table->bigInteger('memo_id');
            $table->integer('onucched_no')->nullable();
            $table->string('memo_irregularity_type');
            $table->string('memo_irregularity_sub_type');
            $table->integer('ministry_id');
            $table->string('ministry_name_en');
            $table->string('ministry_name_bn');
            $table->bigInteger('parent_office_id');
            $table->string('parent_office_name_en');
            $table->string('parent_office_name_bn');
            $table->bigInteger('cost_center_id');
            $table->string('cost_center_name_en');
            $table->string('cost_center_name_bn');
            $table->bigInteger('fiscal_year_id');
            $table->year('audit_year_start');
            $table->year('audit_year_end');
            $table->smallInteger('ac_query_potro_no');
            $table->bigInteger('ap_office_order_id');
            $table->bigInteger('audit_plan_id');
            $table->string('audit_type');
            $table->bigInteger('team_id');
            $table->text('memo_title_bn');
            $table->longText('memo_description_bn');
            $table->string('memo_type');
            $table->string('memo_status');
            $table->double('jorito_ortho_poriman');
            $table->double('onishponno_jorito_ortho_poriman')->nullable();
            $table->text('response_of_rpu')->nullable();
            $table->text('audit_conclusion')->nullable();
            $table->text('audit_recommendation')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->string('approve_status');
            $table->string('status');
            $table->text('comment')->nullable();
            $table->integer('sender_officer_id');
            $table->string('sender_officer_name_bn');
            $table->string('sender_officer_name_en');
            $table->integer('sender_unit_id');
            $table->string('sender_unit_name_bn');
            $table->string('sender_unit_name_en');
            $table->integer('sender_designation_id');
            $table->string('sender_designation_bn');
            $table->string('sender_designation_en');
            $table->integer('rpu_acceptor_officer_id');
            $table->string('rpu_acceptor_officer_name_en');
            $table->string('rpu_acceptor_officer_name_bn');
            $table->string('rpu_acceptor_unit_name_en');
            $table->string('rpu_acceptor_unit_name_bn');
            $table->string('rpu_acceptor_designation_name_bn');
            $table->string('rpu_acceptor_designation_name_en');
            $table->longText('rpu_acceptor_signature');
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
        Schema::dropIfExists('apotti_items');
    }
}
