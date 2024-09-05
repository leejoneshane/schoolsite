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
            $table->string('title');
            $table->string('image_file')->nullable();
            $table->integer('party_id')->nullable();
            $table->integer('class_id');
            $table->integer('level')->default(1);
            $table->integer('xp');
            $table->integer('max_hp');
            $table->integer('hp');
            $table->integer('max_mp');
            $table->integer('mp');
            $table->integer('ap');
            $table->integer('dp');
            $table->integer('sp');
            $table->integer('gp');
            $table->enum('temp_effect', ['ap', 'dp', 'sp']);
            $table->float('effect_value')->default(0);
            $table->timestamp('effect_timeout')->nullable();
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
