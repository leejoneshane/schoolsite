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
        Schema::create('public_class', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->integer('domain_id');
            $table->string('teach_unit');
            $table->integer('teach_grade');
            $table->integer('teach_class');
            $table->date('reserved_at');
            $table->integer('weekday');
            $table->integer('session');
            $table->string('place')->nullable();
            $table->uuid('uuid');
            $table->json('partners')->nullable();
            $table->string('eduplan')->nullable();
            $table->string('discuss')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_class');
    }
};
