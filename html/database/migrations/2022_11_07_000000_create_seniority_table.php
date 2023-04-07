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
        Schema::create('seniority', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('syear');
            $table->integer('school_year');
            $table->integer('school_month');
            $table->float('school_score');
            $table->integer('teach_year');
            $table->integer('teach_month');
            $table->float('teach_score');
            $table->boolean('ok')->default(0);
            $table->integer('new_school_year')->nullable();
            $table->integer('new_school_month')->nullable();
            $table->float('new_school_score')->nullable();
            $table->integer('new_teach_year')->nullable();
            $table->integer('new_teach_month')->nullable();
            $table->float('new_teach_score')->nullable();
            $table->timestamps();
            $table->unique(['uuid', 'syear']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seniority');
    }
};