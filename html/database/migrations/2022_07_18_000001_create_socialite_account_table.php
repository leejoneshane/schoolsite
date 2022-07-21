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
        Schema::create('socialite_account', function (Blueprint $table) {
            $table->uuid('uuid')->index();
			$table->string('socialite');
			$table->string('userId',255);
            $table->timestamps();
            $table->index(['uuid', 'socialite'])->unique();
            $table->index(['socialite', 'userId'])->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialite_account');
    }
};