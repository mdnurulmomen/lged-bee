<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadSheetReplyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('broad_sheet_reply_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('broad_sheet_reply_id');
            $table->bigInteger('apotti_item_id');
            $table->bigInteger('memo_id');
            $table->string('status')->nullable();
            $table->string('approval_status')->nullable();
            $table->double('jorito_ortho_poriman')->nullable();
            $table->double('onishponno_jorito_ortho_poriman')->nullable();
            $table->bigInteger('approved_by')->nullable();
            $table->string('approver_bn')->nullable();
            $table->string('approver_en')->nullable();
            $table->bigInteger('approver_designation_id')->nullable();
            $table->string('approver_designation_bn')->nullable();
            $table->string('approver_designation_en')->nullable();
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
        Schema::dropIfExists('broad_sheet_reply_items');
    }
}
