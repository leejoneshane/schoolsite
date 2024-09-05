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
        Schema::create('game_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_file')->nullable();
            $table->float('hp_lvlup')->default(0);
            $table->float('mp_lvlup')->default(0);
            $table->float('ap_lvlup')->default(0);
            $table->float('dp_lvlup')->default(0);
            $table->float('sp_lvlup')->default(0);
            $table->integer('base_hp')->default(0);
            $table->integer('base_mp')->default(0);
            $table->integer('base_ap')->default(0);
            $table->integer('base_dp')->default(0);
            $table->integer('base_sp')->default(0);
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
        Schema::dropIfExists('game_classes');
    }
};
