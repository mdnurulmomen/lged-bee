<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApPsrSubjectMattersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ap_psr_subject_matters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('annual_plan_main_id')->nullable();
            $table->bigInteger('annual_plan_id');
            $table->string('vumika');
            $table->string('subject_matter_en');
            $table->string('subject_matter_bn');
            $table->integer('parent_id')->nullable();
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
        Schema::dropIfExists('ap_psr_subject_matters');
    }
}
