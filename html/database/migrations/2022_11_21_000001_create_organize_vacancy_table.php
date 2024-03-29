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
        Schema::create('organize_vacancy', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->enum('type', ['admin', 'tutor', 'domain']);
            $table->integer('role_id')->nullable();
            $table->string('grade_id')->nullable();
            $table->integer('domain_id')->nullable();
            $table->boolean('special')->default(0);
            $table->string('name');
            $table->integer('stage');
            $table->integer('shortfall')->default(0);
            $table->integer('filled')->default(0);
            $table->integer('assigned')->default(0);
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
        Schema::dropIfExists('organize_vacancy');
    }
};
