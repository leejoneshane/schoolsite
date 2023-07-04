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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('short_name', 10);
            $table->integer('kind_id');
            $table->integer('unit_id');
            $table->json('for_grade');
            $table->boolean('self_remove')->default(1);
            $table->boolean('has_lunch')->default(0);
            $table->boolean('divide')->default(0);
            $table->boolean('stop_enroll')->default(0);
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
        Schema::dropIfExists('clubs');
    }
};