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
        Schema::create('game_settings', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('description');
            $table->enum('type', ['positive', 'negative']);
            $table->float('effect_xp')->default(0);
            $table->float('effect_gp')->default(0);
            $table->float('effect_hp')->default(0);
            $table->float('effect_mp')->default(0);
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
        Schema::dropIfExists('game_settings');
    }
};
