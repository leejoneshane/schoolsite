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
        Schema::create('clubs_section', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->integer('club_id');
            $table->json('weekdays')->nullable();
            $table->boolean('self_defined')->default(0);
            $table->date('startDate');
            $table->date('endDate');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('teacher')->nullable();
            $table->string('location')->nullable();
            $table->text('memo')->nullable();
            $table->integer('cash');
            $table->integer('total');
            $table->integer('maximum');
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
        Schema::dropIfExists('clubs_section');
    }
};