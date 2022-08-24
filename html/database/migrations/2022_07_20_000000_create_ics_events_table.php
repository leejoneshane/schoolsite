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
        Schema::create('ics_events', function (Blueprint $table) {
            $table->increments('id');
			$table->string('unit_id')->index();
            $table->boolean('all_day');
			$table->date('startDate')->useCurrent();
			$table->date('endDate')->useCurrent();
			$table->time('startTime');
			$table->time('endTime');
            $table->text('summary');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('calendar_id')->index();
            $table->string('event_id')->nullable();
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
        Schema::dropIfExists('ics_events');
    }
};