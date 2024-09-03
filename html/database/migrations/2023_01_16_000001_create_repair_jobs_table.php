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
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('reporter_name')->nullable();
            $table->integer('kind_id');
            $table->string('place');
            $table->string('summary');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('repair_jobs');
    }
};
