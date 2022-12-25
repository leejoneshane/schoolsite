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
        Schema::create('venue_reserved', function (Blueprint $table) {
            $table->id();
            $table->integer('venue_id');
            $table->uuid('uuid');
            $table->date('reserved_at');
            $table->integer('weekday');
            $table->integer('session');
            $table->integer('length')->default(1);
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('venue_reserved');
    }
};
