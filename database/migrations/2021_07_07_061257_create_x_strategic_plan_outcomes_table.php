<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXStrategicPlanOutcomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_strategic_plan_outcomes', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->string('outcome_no', 16);
            $table->string('outcome_title_en');
            $table->string('outcome_title_bn');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('x_strategic_plan_outcomes');
    }
}
