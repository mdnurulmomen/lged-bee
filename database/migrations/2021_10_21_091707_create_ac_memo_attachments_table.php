<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcMemoAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('ac_memo_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ac_memo_id');
            $table->string('attachment_type', 20);
            $table->longText('user_define_name');
            $table->longText('attachment_name');
            $table->longText('attachment_path');
            $table->smallInteger('sequence');
            $table->bigInteger('deleted_by');
            $table->softDeletes();
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
        Schema::dropIfExists('ac_memo_attachments');
    }
}
