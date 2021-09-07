<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('annual_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('schedule_id');
            $table->bigInteger('activity_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('fiscal_year_id');
            $table->string('ministry_name_en');
            $table->string('ministry_name_bn');
            $table->bigInteger('ministry_id');
            $table->bigInteger('controlling_office_id');
            $table->string('controlling_office_en');
            $table->string('controlling_office_bn');
            $table->string('office_type');
            $table->string('total_unit_no');
            $table->json('nominated_offices');
            $table->tinyInteger('nominated_office_counts');
            $table->string('subject_matter');
            $table->json('nominated_man_powers');
            $table->tinyInteger('nominated_man_power_counts');
            $table->json('comment');
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
        Schema::dropIfExists('annual_plans');
    }
}
