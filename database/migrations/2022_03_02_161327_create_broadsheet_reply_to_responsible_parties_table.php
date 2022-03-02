<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadsheetReplyToResponsiblePartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('broadsheet_reply_to_responsible_parties', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('broad_sheet_reply_id');
            $table->string('ref_memorandum_no');
            $table->string('memorandum_no');
            $table->date('memorandum_date');
            $table->text('rpu_office_head_details');
            $table->text('subject');
            $table->text('description');
            $table->text('braod_sheet_cc');
            $table->bigInteger('sender_id');
            $table->string('sender_name_bn');
            $table->string('sender_name_en');
            $table->bigInteger('sender_designation_id');
            $table->string('sender_designation_bn');
            $table->string('sender_designation_en');
            $table->bigInteger('sender_unit_id');
            $table->string('sender_unit_bn');
            $table->string('sender_unit_en');
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
        Schema::dropIfExists('broadsheet_reply_to_responsible_parties');
    }
}
