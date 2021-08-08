<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpOutputIndicatorDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp_output_indicator_details', function (Blueprint $table) {
            $table->id();
            $table->integer('output_indicator_id');
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('output_id');
            $table->string('unit_type');
            $table->string('target_value')->nullable();
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
        Schema::dropIfExists('output_indicator_details');
    }
}
