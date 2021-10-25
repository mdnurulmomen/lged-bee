<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcMemoRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ac_memo_recommendations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('memo_id');
            $table->text('audit_recommendation');
            $table->bigInteger('created_by');
            $table->string('created_by_name_en');
            $table->string('created_by_name_bn');
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
        Schema::dropIfExists('ac_memo_recommendations');
    }
}
