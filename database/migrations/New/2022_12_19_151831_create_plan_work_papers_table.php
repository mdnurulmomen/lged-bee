<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanWorkPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('plan_work_papers', function (Blueprint $table) {
            $table->text('title_en')->nullable();
            $table->text('title_bn')->nullable();
            $table->text('attachment');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->bigInteger('audit_plan_id');
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
        Schema::dropIfExists('plan_work_papers');
    }
}
