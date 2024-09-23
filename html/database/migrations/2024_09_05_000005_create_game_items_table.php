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
        Schema::create('game_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('image_file')->nullable();
            $table->boolean('passive')->default(0);
            $table->enum('object', ['self', 'party', 'partner', 'enemy', 'all']);
            $table->float('hit_rate')->default(1);
            $table->float('hp')->default(0);
            $table->float('mp')->default(0);
            $table->float('ap')->default(0);
            $table->float('dp')->default(0);
            $table->float('sp')->default(0);
            $table->string('status')->nullable();
            $table->integer('gp')->default(0);
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
        Schema::dropIfExists('game_items');
    }
};
