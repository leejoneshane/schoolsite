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
        Schema::create('game_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('dungeon_id');
            $table->integer('evaluate_id');
            $table->string('classroom_id');
            $table->integer('seat');
            $table->string('student');
            $table->integer('score')->default(0);
            $table->timestamp('tested_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_answers');
    }
};
