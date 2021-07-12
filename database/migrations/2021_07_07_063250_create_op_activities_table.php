<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_id');
            $table->integer('fiscal_year_id');
            $table->integer('outcome_id');
            $table->integer('output_id');
            $table->string('activity_no', 16);
            $table->string('title_en');
            $table->string('title_bn');
            $table->integer('activity_parent_id')->default(0);
            $table->boolean('is_parent')->default(0)->comment('parent/child');
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
        Schema::dropIfExists('op_activities');
    }
}
