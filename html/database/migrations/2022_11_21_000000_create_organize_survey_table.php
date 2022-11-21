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
        Schema::create('organize_survey', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->uuid('uuid');
            $table->integer('age');
            $table->text('exprience');
            $table->integer('edu_level');
            $table->string('edu_school');
            $table->string('edu_division');
            $table->float('score');
            $table->string('admin1');
            $table->string('admin2');
            $table->string('admin3');
            $table->string('computer');
            $table->string('mind');
            $table->string('lib');
            $table->string('cali');
            $table->string('teach1');
            $table->string('teach2');
            $table->string('teach3');
            $table->string('teach4');
            $table->string('teach5');
            $table->string('teach6');
            $table->integer('grade');
            $table->boolean('overcome');
            $table->string('assign');
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
        Schema::dropIfExists('organize_survey');
    }
};
