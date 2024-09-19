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
        Schema::create('game_characters', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->integer('party_id')->nullable();
            $table->integer('seat');
            $table->string('title')->nullable();
            $table->string('name');
            $table->integer('class_id')->nullable();
            $table->integer('image_id')->nullable();
            $table->integer('level')->default(1);
            $table->integer('xp')->default(0);
            $table->integer('max_hp')->default(0);
            $table->float('hp')->default(0);
            $table->integer('max_mp')->default(0);
            $table->float('mp')->default(0);
            $table->integer('ap')->default(0);
            $table->integer('dp')->default(0);
            $table->integer('sp')->default(0);
            $table->integer('gp')->default(0);
            $table->string('temp_effect')->nullable();
            $table->float('effect_value')->default(0);
            $table->timestamp('effect_timeout')->nullable();
            $table->string('buff')->nullable();
            $table->boolean('absent')->default(0);
            $table->integer('pick_up')->default(0);
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
        Schema::dropIfExists('game_characters');
    }
};
