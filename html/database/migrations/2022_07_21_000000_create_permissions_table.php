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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
			$table->string('group')->index();
			$table->string('permission');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['group', 'permission']);
        });
        DB::table('permissions')->insert([
            ['group' => 'perm', 'permission' => 'admin', 'description' => '管理所有權限', ],
            ['group' => 'perm', 'permission' => 'assign', 'description' => '授權或取消使用者的權限', ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};