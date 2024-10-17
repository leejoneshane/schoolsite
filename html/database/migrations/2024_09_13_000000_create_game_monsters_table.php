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
            $table->integer('min_level')->default(100);
            $table->integer('max_level')->default(100);
            $table->integer('hp')->default(100);
            $table->float('crit_rate')->default(0);
            $table->integer('ap')->default(0);
            $table->integer('dp')->default(0);
            $table->integer('sp')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('gp')->default(0);
            $table->string('style')->nullable();
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
