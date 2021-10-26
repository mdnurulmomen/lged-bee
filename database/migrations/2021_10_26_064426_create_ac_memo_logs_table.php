<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcMemoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ac_memo_logs', function (Blueprint $table) {
            $table->id();
            $table->longText('memo_content_change');
            $table->longText('memo_file_change');
            $table->bigInteger('created_by_id');
            $table->string('created_by_name');
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
        Schema::dropIfExists('ac_memo_logs');
    }
}
