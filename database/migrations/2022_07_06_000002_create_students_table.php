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
        Schema::create('teachers', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->char('idno',10)->unique();
            $table->string('account')->unique();
            $table->string('sn');
            $table->string('gn');
            $table->string('realname');
            $table->string('class');
            $table->string('seat');
            $table->timestamp('birthdate')->nullable();
            $table->integer('gender');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('telephone')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('www', 255)->nullable();
            $table->string('character', 255)->nullable();
            $table->timestamp('fetch_date')->nullable();
            $table->boolean('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};
