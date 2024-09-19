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
        Schema::create('game_parties', function (Blueprint $table) {
            $table->id();
            $table->integer('classroom_id');
            $table->integer('group_no');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('base_id')->nullable();
            $table->float('effect_hp')->default(0);
            $table->float('effect_mp')->default(0);
            $table->float('effect_ap')->default(0);
            $table->float('effect_dp')->default(0);
            $table->float('effect_sp')->default(0);
            $table->integer('treasury')->default(0);
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
        Schema::dropIfExists('game_parties');
    }
};
