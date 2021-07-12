<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpActivityMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_activity_milestones', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year_id');
            $table->integer('duration_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->integer('activity_id');
            $table->string('title_en');
            $table->string('title_bn');
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
        Schema::dropIfExists('op_activity_milestones');
    }
}
