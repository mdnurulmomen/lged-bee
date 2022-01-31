<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQacCommitteeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('qac_committee_members', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year_id');
            $table->string('qac_type');
            $table->bigInteger('qac_committee_id');
            $table->bigInteger('officer_id');
            $table->string('officer_bn');
            $table->string('officer_en');
            $table->bigInteger('officer_designation_grade');
            $table->bigInteger('officer_designation_id');
            $table->string('officer_designation_bn');
            $table->string('officer_designation_en');
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
        Schema::dropIfExists('qac_committee_members');
    }
}
