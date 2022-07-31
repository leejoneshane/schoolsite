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
        Schema::create('menus', function (Blueprint $table) {
            $table->string('id')->primary();
			$table->string('parent_id')->nullable();
			$table->string('caption');
            $table->string('url', 255)->default('#');
            $table->integer('weight')->default(0);
            $table->timestamps();
        });
        DB::table('menus')->insert([
            [ 'id' => 'main', 'caption' => '主選單', ],
            [ 'id' => 'admin', 'caption' => '管理選單', ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};