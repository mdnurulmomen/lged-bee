<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaCommitteeMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('BeeCoreDB')->create('pac_meetings', function (Blueprint $table) {
            $table->id();
            $table->integer('directorate_id');
            $table->string('directorate_bn');
            $table->string('directorate_en');
            $table->string('meeting_no');
            $table->date('meeting_date');
            $table->string('parliament_no');
            $table->integer('final_report_id');
            $table->string('meeting_place');
            $table->longText('meeting_description')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('created_by');
            $table->string('created_by_bn');
            $table->string('created_by_en');
            $table->bigInteger('updated_by');
            $table->string('updated_by_bn');
            $table->string('updated_by_en');
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
        Schema::dropIfExists('pa_committee_meetings');
    }
}
