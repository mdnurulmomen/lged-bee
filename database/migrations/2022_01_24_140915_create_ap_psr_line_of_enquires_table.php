<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApPsrLineOfEnquiresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

         Schema::connection('OfficeDB')->create('ap_psr_line_of_enquires', function (Blueprint $table) {
            $table->id();
            $table->integer('sub_objective_id');
            $table->string('line_of_enquire_en');
            $table->string('line_of_enquire_bn');
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
        Schema::dropIfExists('ap_psr_line_of_enquires');
    }
}
