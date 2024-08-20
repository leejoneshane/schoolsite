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
        Schema::create('lunch_survey', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->uuid('uuid');
            $table->string('class_id');
            $table->integer('seat');
            $table->boolean('by_school')->default(1);
            $table->boolean('vegen')->default(0);
            $table->boolean('milk')->default(1);
            $table->boolean('by_parent')->default(0);
            $table->boolean('boxed_meal')->default(0);
            $table->timestamps();
            $table->unique(['section', 'uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lunch_survey');
    }
};
