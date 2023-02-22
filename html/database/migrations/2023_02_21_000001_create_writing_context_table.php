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
            $table->uuid('uuid');
            $table->string('title');
            $table->text('words');
            $table->string('author');
            $table->string('classname');
            $table->integer('hits');
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
