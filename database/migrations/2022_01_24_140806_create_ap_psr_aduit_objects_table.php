<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApPsrAduitObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('OfficeDB')->create('ap_psr_aduit_objects', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('annual_plan_main_id')->nullable();
                $table->bigInteger('annual_plan_id');
                $table->string('audit_objective_en');
                $table->string('audit_objective_bn');
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
        Schema::dropIfExists('ap_psr_aduit_objects');
    }
}
