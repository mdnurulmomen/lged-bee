<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_programs', function (Blueprint $table) {
            $table->id();
            $table->float('area_index');
            $table->string('category');
            $table->string('control_objective');
            $table->unsignedInteger('audit_area_id');
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
        Schema::dropIfExists('audit_programs');
    }
}
