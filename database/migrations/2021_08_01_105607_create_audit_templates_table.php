<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 128);
            $table->string('template_type', 32);
            $table->longText('content');
            $table->string('lang', 3)->default('bn')->comment('language');
            $table->tinyInteger('version')->nullable()->comment('template version');
            $table->boolean('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
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
        Schema::dropIfExists('audit_templates');
    }
}
