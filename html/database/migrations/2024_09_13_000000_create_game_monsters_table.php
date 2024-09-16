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
        Schema::create('game_monsters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('max_hp')->default(100);
            $table->float('hp')->default(0);
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
        Schema::dropIfExists('game_monsters');
    }
};
