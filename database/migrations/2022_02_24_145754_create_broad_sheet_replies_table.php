<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadSheetRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('broad_sheet_replies', function (Blueprint $table) {
            $table->id();
            $table->string('memorandum_no');
            $table->date('memorandum_date');
            $table->bigInteger('sender_office_id');
            $table->string('sender_office_name_bn');
            $table->string('sender_office_name_en');
            $table->string('sender_type');
            $table->string('sender_name_bn')->nullable();
            $table->string('sender_name_en')->nullable();
            $table->string('sender_designation_bn')->nullable();
            $table->string('sender_designation_en')->nullable();
            $table->string('sender_unit_bn')->nullable();
            $table->string('sender_unit_en')->nullable();
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
        Schema::dropIfExists('broad_sheet_replies');
    }
}
