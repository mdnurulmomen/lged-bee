<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditObservationCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_observation_communications', function (Blueprint $table) {
            $table->id();
            $table->integer('observation_id');
            $table->integer('parent_office_id');
            $table->integer('rp_office_id');
            $table->integer('directorate_id');
            $table->string('message_title');
            $table->text('message_body');
            $table->integer('sent_to');
            $table->integer('sent_by');
            $table->integer('status');
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
        Schema::dropIfExists('audit_observation_communications');
    }
}
