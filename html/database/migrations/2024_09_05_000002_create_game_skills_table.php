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
        Schema::create('game_skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('gif_file')->nullable();
            $table->boolean('passive')->default(0);
            $table->enum('object', ['self', 'party', 'partner', 'enemy', 'all']);
            $table->float('hit_rate')->default(1);
            $table->integer('cost_mp')->default(0);
            $table->float('ap')->default(0);
            $table->float('steal_hp')->default(0);
            $table->float('steal_mp')->default(0);
            $table->float('steal_gp')->default(0);
            $table->float('effect_hp')->default(0);
            $table->float('effect_mp')->default(0);
            $table->float('effect_ap')->default(0);
            $table->float('effect_dp')->default(0);
            $table->float('effect_sp')->default(0);
            $table->integer('effect_times')->default(40);
            $table->string('status')->nullable();
            $table->string('inspire')->nullable();
            $table->integer('earn_xp')->default(0);
            $table->integer('earn_gp')->default(0);
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
        Schema::dropIfExists('game_skills');
    }
};
