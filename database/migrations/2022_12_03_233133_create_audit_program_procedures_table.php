<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditProgramProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('OfficeDB')->create('audit_program_procedures', function (Blueprint $table) {
            $table->id();
            $table->text('test_procedure');
            $table->text('note')->nullable();
            $table->string('done_by')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedInteger('audit_program_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_program_procedures');
    }
}
