<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_menus', function (Blueprint $table) {
            $table->id();
            $table->string('menu_name_en');
            $table->string('menu_name_bn');
            $table->string('menu_class')->nullable();
            $table->string('menu_link')->nullable();
            $table->string('menu_controller')->nullable();
            $table->string('menu_method')->nullable();
            $table->string('menu_icon')->nullable();
            $table->bigInteger('module_menu_id')->nullable();
            $table->bigInteger('parent_menu_id')->nullable();
            $table->string('display_order')->default(1);
            $table->string('status')->default(1);
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
        Schema::dropIfExists('p_menus');
    }
}
