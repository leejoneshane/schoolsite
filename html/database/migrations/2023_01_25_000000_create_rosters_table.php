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
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('grades')->nullable();
            $table->json('fields')->nullable();
            $table->json('domains')->nullable();
            $table->date('started_at');
            $table->date('ended_at');
            $table->integer('min')->default(0);
            $table->integer('max')->default(0);
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
        Schema::dropIfExists('rosters');
    }
};
