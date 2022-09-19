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
        Schema::create('club_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('single');
            $table->boolean('stop_enroll');
            $table->boolean('manual_auditing');
            $table->date('enrollDate');
			$table->date('expireDate');
            $table->time('workTime')->nullable();
			$table->time('restTime')->nullable();
            $table->string('style');
            $table->integer('weight');
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
        Schema::dropIfExists('club_kinds');
    }
};