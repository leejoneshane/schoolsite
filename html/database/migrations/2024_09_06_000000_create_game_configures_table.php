<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_configures', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->integer('classroom_id');
            $table->integer('daily_mp')->default(8);
            $table->boolean('change_base')->default(0);
            $table->boolean('change_class')->default(0);
            $table->boolean('arena_open')->default(1);
            $table->boolean('furniture_shop')->default(1);
            $table->boolean('item_shop')->default(1);
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
        Schema::dropIfExists('game_configures');
    }
};
