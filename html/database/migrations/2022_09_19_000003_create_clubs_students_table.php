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
        Schema::create('clubs_students', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->uuid();
            $table->integer('club_id');
            $table->integer('need_lunch')->default(0);
            $table->json('weekdays')->nullable();
            $table->integer('identity')->default(0);
            $table->string('email', 100)->nullable();
            $table->string('parent')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('accepted')->default(0);
            $table->string('groupBy', 2)->nullable();
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
        Schema::dropIfExists('clubs_students');
    }
};