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
        Schema::create('game_delays', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->integer('classroom_id');
            $table->uuid('uuid');
            $table->json('characters');
            $table->integer('rule')->nullable();
            $table->string('reason')->nullable();
            $table->integer('hp')->default(0);
            $table->integer('mp')->default(0);
            $table->boolean('act')->default(0);
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
        Schema::dropIfExists('game_delays');
    }
};
