<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXStrategicPlanRequiredCapacitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_strategic_plan_required_capacities', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('outcome_id');
            $table->string('capacity_no', 16);
            $table->string('title_en');
            $table->string('title_bn');
            $table->integer('remarks');
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
        Schema::dropIfExists('x_strategic_plan_required_capacities');
    }
}
