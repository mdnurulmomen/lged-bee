<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePMenuActionRoleMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_menu_action_role_maps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('p_role_id');
            $table->bigInteger('p_menu_action_id');
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
        Schema::dropIfExists('p_menu_action_role_maps');
    }
}
