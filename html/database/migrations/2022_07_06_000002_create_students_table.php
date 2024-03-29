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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->char('idno',10)->unique();
            $table->string('account')->unique();
            $table->string('id')->unique();
            $table->string('sn');
            $table->string('gn');
            $table->string('realname');
            $table->string('class_id');
            $table->integer('seat');
            $table->date('birthdate')->nullable();
            $table->integer('gender');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('telephone')->nullable();
            $table->text('address')->nullable();
            $table->text('www')->nullable();
            $table->text('character')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
