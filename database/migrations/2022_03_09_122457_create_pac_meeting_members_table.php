<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacMeetingMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('BeeCoreDB')->create('pac_meeting_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pac_meeting_id');
            $table->string('member_type');
            $table->bigInteger('member_id');
            $table->string('member_bn')->nullable();
            $table->string('member_en')->nullable();
            $table->integer('designation_id')->nullable();
            $table->string('designation_en')->nullable();
            $table->string('designation_bn')->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('unit_en')->nullable();
            $table->string('unit_bn')->nullable();
            $table->string('meeting_designation')->nullable();
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
        Schema::dropIfExists('pac_meeting_members');
    }
}
