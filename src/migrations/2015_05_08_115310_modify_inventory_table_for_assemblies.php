<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyInventoryTableForAssemblies extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tb_inventory_inventories', function (Blueprint $table) {
            $table->boolean('is_assembly')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tb_inventory_inventories', function (Blueprint $table) {
            $table->dropColumn('is_assembly');
        });
    }
}
