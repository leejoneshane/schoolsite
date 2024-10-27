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
        Schema::create('game_processes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('classroom_id');
            $table->integer('seat');
            $table->string('student');
            $table->integer('adventure_id');
            $table->integer('worksheet_id');
            $table->integer('task_id');
            $table->timestamp('completed_at')->useCurrent();
            $table->string('comments')->nullable();
            $table->boolean('noticed')->default(0);
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_processes');
    }
};
