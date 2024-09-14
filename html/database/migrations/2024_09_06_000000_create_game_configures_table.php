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
            $table->integer('daily_mp')->default(4);
            $table->boolean('regroup')->default(1);
            $table->boolean('change_base')->default(1);
            $table->boolean('change_class')->default(1);
            $table->boolean('arena_open')->default(1);
            $table->boolean('furniture_shop')->default(1);
            $table->boolean('item_shop')->default(1);
            $table->boolean('pet_shop')->default(0);
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
