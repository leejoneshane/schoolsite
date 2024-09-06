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
            $table->uuid('classroom_id')->primary();
            $table->integer('daily_mp');
            $table->boolean('regroup');
            $table->boolean('change_base');
            $table->boolean('change_class');
            $table->boolean('arena_open');
            $table->boolean('dungeon_open');
            $table->boolean('furniture_shop');
            $table->boolean('item_shop');
            $table->boolean('pet_shop');
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
