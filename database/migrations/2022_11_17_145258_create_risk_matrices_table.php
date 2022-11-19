<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskMatricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('x_risk_assessment_likelihood_id');
            $table->string('x_risk_assessment_impact_id');
            $table->string('x_risk_level_id');
            $table->unsignedTinyInteger('priority')->default(0);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
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
        Schema::dropIfExists('risk_matrices');
    }
}
