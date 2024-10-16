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
        Schema::create('game_monster_spawns', function (Blueprint $table) {
            $table->id();
            $table->integer('monster_id');
            $table->string('name');
            $table->integer('level');
            $table->string('url');
            $table->integer('max_hp');
            $table->integer('hp')->default(100);
            $table->float('hit_rate')->default(0);
            $table->float('crit_rate')->default(0);
            $table->integer('ap')->default(0);
            $table->integer('dp')->default(0);
            $table->integer('sp')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('gp')->default(0);
            $table->string('temp_effect')->nullable();
            $table->float('effect_value')->default(0);
            $table->timestamp('effect_timeout')->nullable();
            $table->string('buff')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_monsters');
    }
};
