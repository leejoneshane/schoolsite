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
        Schema::create('students_clubs', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->uuid();
            $table->integer('club_id');
            $table->integer('need_lunch')->default(0);
            $table->json('weekdays')->nullable();
            $table->integer('identity')->default(0);
            $table->string('email', 100);
            $table->string('parent');
            $table->string('mobile');
            $table->boolean('accepted');
            $table->timestamp('audited_at')->nullable();
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
        Schema::dropIfExists('students_clubs');
    }
};