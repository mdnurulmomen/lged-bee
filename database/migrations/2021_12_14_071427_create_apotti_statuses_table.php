<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApottiStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('apotti_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('apotti_id');
            $table->string('apotti_type');
            $table->string('qac_type');
            $table->integer('created_by');
            $table->string('created_by_name_en');
            $table->string('created_by_name_bn');
            $table->integer('updated_by')->nullable();
            $table->string('updated_by_name_en')->nullable();
            $table->string('updated_by_name_bn')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('apotti_statuses');
    }
}
