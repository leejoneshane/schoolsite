<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lunch_teachers', function (Blueprint $table) {
            $table->id();
            $table->string('section')->comment('學期代碼');
            $table->string('uuid')->comment('教師唯一代碼');
            $table->boolean('tutor')->default(false)->comment('是否為導師');
            $table->json('weekdays')->nullable()->comment('週一至週五用餐狀態 [boolean]');
            $table->json('places')->nullable()->comment('週一至週五用餐地點 ID [integer]'); // Assuming IDs of LunchCafeteria
            $table->boolean('vegen')->default(false)->comment('是否素食');
            $table->boolean('milk')->default(true)->comment('是否喝牛奶');
            $table->timestamps();
            $table->unique(['section', 'uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lunch_teachers');
    }
};
