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
        Schema::create('organize_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->date('survey_at');
            $table->date('first_stage');
            $table->date('pause_at');
            $table->date('second_stage');
            $table->date('close_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organize_settings');
    }
};
