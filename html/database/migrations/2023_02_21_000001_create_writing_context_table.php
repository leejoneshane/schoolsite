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
        Schema::create('writing_context', function (Blueprint $table) {
            $table->id();
            $table->integer('genre_id');
            $table->uuid('uuid')->nullable();
            $table->string('title');
            $table->text('words');
            $table->string('author')->nullable();
            $table->string('classname')->nullable();
            $table->integer('hits')->default(0);
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
        Schema::dropIfExists('writing_context');
    }
};
