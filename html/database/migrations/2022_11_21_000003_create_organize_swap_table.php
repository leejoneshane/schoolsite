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
        Schema::create('organize_swap', function (Blueprint $table) {
            $table->id();
            $table->integer('syear');
            $table->uuid('uuid');
            $table->integer('vacancy_id');
            $table->unique(['syear', 'uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organize_swap');
    }
};
