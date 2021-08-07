<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomeIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcome_indicators', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('outcome_id');
            $table->string('name_en');
            $table->string('name_bn');
            $table->string('frequency_en');
            $table->string('frequency_bn');
            $table->string('datasource_en');
            $table->string('datasource_bn');
            $table->integer('base_fiscal_year_id');
            $table->string('base_value')->nullable();
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
        Schema::dropIfExists('outcome_indicators');
    }
}
