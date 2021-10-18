<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('BeeCoreDB')->create('queries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cost_center_type_id');
            $table->string('query_title_en');
            $table->string('query_title_bn');
            $table->mediumText('query_description_en');
            $table->mediumText('query_description_bn');
            $table->boolean('is_global');
            $table->bigInteger('office_id');
            $table->bigInteger('created_by');
            $table->bigInteger('modified_by');
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
        Schema::dropIfExists('queries');
    }
}
