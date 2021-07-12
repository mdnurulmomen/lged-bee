<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXStrategicPlanOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_strategic_plan_outputs', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('outcome_id');
            $table->string('output_no', 16);
            $table->string('output_title_en');
            $table->string('output_title_bn');
            $table->text('remarks');
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
        Schema::dropIfExists('x_strategic_plan_outputs');
    }
}
