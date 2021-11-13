<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_menu_modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name_en');
            $table->string('module_name_bn');
            $table->boolean('is_other_module')->default(0);
            $table->string('module_link')->nullable();
            $table->string('module_class')->nullable();
            $table->string('module_controller')->nullable();
            $table->string('module_method')->nullable();
            $table->string('module_icon')->nullable();
            $table->string('display_order')->default(1);
            $table->string('status')->default(1);
            $table->bigInteger('parent_module_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('modified_by')->nullable();
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
        Schema::dropIfExists('p_menu_modules');
    }
}
