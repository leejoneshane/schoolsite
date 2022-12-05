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
            $table->text('exprience')->nullable();
            $table->integer('edu_level');
            $table->string('edu_school')->nullable();
            $table->string('edu_division')->nullable();
            $table->float('score');
            $table->string('admin1')->nullable();
            $table->string('admin2')->nullable();
            $table->string('admin3')->nullable();
            $table->json('special')->nullable();
            $table->string('teach1')->nullable();
            $table->string('teach2')->nullable();
            $table->string('teach3')->nullable();
            $table->string('teach4')->nullable();
            $table->string('teach5')->nullable();
            $table->string('teach6')->nullable();
            $table->integer('grade')->default(2);
            $table->boolean('overcome')->default(0);
            $table->string('assign')->nullable();
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
