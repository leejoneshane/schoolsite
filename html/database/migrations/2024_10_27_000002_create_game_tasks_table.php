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
        Schema::create('game_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('worksheet_id');
            $table->integer('next_task')->nullable();
            $table->integer('coordinate_x');
            $table->integer('coordinate_y');
            $table->string('story')->nullable();
            $table->string('task')->nullable();
            $table->boolean('review')->default(1);
            $table->integer('reward_xp')->default(0);
            $table->integer('reward_gp')->default(0);
            $table->integer('reward_item')->nullable();
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
        Schema::dropIfExists('game_tasks');
    }
};
