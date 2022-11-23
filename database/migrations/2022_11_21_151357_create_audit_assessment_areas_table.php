<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditAssessmentAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_assessment_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('x_audit_area_id');
            $table->unsignedInteger('assessment_item_id');
            $table->string('assessment_item_type');        // projects / functions / unit /
            $table->boolean('is_latest')->default(1);
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('updater_id');
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
        Schema::dropIfExists('audit_assessment_areas');
    }
}
