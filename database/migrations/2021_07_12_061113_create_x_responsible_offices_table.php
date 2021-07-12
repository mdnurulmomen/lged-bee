<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXResponsibleOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_responsible_offices', function (Blueprint $table) {
            $table->id();
            $table->integer('office_id');
            $table->string('office_name_en');
            $table->string('office_name_bn');
            $table->char('short_name_en', 32);
            $table->char('short_name_bn', 32);
            $table->integer('office_sequence');
            $table->tinyInteger('office_layer');
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
        Schema::dropIfExists('x_responsible_offices');
    }
}
