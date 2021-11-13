<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name_en');
            $table->string('role_name_bn');
            $table->string('description_en');
            $table->string('description_bn');
            $table->smallInteger('user_level');
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
        Schema::dropIfExists('p_roles');
    }
}
