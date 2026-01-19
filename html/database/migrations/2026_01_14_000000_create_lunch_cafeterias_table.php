<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLunchCafeteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lunch_cafeterias', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->timestamps();
        });
        DB::table('lunch_cafeterias')->insert([
            ['id' => '1', 'description' => '隨班用餐'],
            ['id' => '2', 'description' => '樂學樓1F'],
            ['id' => '3', 'description' => '教學研究室2F'],
            ['id' => '4', 'description' => '科任辦公室3F'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lunch_cafeterias');
    }
}
