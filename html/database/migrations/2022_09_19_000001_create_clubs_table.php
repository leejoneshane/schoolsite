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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('short_name', 10);
            $table->integer('kind_id');
            $table->integer('unit_id');
            $table->json('for_grade');
            $table->json('weekdays');
            $table->boolean('self_defined');
            $table->boolean('self_remove');
            $table->boolean('has_lunch');
            $table->boolean('stop_enroll');
			$table->date('startDate')->useCurrent();
			$table->date('endDate')->useCurrent();
			$table->time('startTime')->nullable();
			$table->time('endTime')->nullable();
            $table->string('teacher');
            $table->string('location');
            $table->text('memo');
            $table->integer('cash');
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
        Schema::dropIfExists('clubs');
    }
};