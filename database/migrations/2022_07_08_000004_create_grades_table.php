<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
        });
        DB::table('grades')->insert([
            ['id' => '1', 'name' => '一年級'],
            ['id' => '2', 'name' => '二年級'],
            ['id' => '3', 'name' => '三年級'],
            ['id' => '4', 'name' => '四年級'],
            ['id' => '5', 'name' => '五年級'],
            ['id' => '6', 'name' => '六年級'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }
};
