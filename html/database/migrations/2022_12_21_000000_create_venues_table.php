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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->uuid('uuid');
            $table->text('description', 500)->nullable();
            $table->json('availability')->nullable();
            $table->date('unavailable_at')->nullable();
            $table->date('unavailable_until')->nullable();
            $table->integer('schedule_start')->default(0);
            $table->integer('schedule_limit')->default(30);
            $table->boolean('open')->default(0);
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
        Schema::dropIfExists('venues');
    }
};
