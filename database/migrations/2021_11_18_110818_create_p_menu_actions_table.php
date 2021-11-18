<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePMenuActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_menu_actions', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_bn');
            $table->string('link')->nullable();
            $table->string('class')->nullable();
            $table->string('controller')->nullable();
            $table->string('method')->nullable();
            $table->string('icon')->nullable();
            $table->string('display_order')->default(1);
            $table->string('status')->default(1);
            $table->bigInteger('parent_id')->nullable();
            $table->boolean('is_other_module')->default(0);
            $table->string('type')->nullable();
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
        Schema::dropIfExists('p_menu_actions');
    }
}
