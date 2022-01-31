<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQacCommitteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('qac_committees', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year_id');
            $table->string('qac_type');
            $table->text('title_bn');
            $table->text('title_en');
            $table->date('date');
            $table->bigInteger('created_by');
            $table->string('created_by_bn');
            $table->string('created_by_en');
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
        Schema::dropIfExists('qac_committees');
    }
}
