<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_observations', function (Blueprint $table) {
            $table->id();
            $table->string('observation_no');
            $table->integer('ministry_id');
            $table->integer('division_id');
            $table->integer('parent_office_id');
            $table->integer('rp_office_id');
            $table->integer('directorate_id');
            $table->integer('team_leader_id');
            $table->string('observation_en')->nullable();
            $table->string('observation_bn')->nullable();
            $table->text('observation_details')->nullable();
            $table->string('observation_type');
            $table->integer('fiscal_year_id');
            $table->float('amount', 10, 2);
            $table->date('initiation_date');
            $table->date('close_date')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('audit_observations');
    }
}
