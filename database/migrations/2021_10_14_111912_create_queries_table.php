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
            $table->string('query_title_en');
            $table->string('query_title_bn');
            $table->mediumText('query_description_en');
            $table->mediumText('query_description_bn');
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
