<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventorySupplierTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tb_inventory_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_fax')->nullable();
            $table->string('contact_email')->nullable();
        });

        Schema::create('tb_inventory_inventory_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('inventory_id')->unsigned();
            $table->integer('supplier_id')->unsigned();

            $table->foreign('inventory_id')->references('id')->on('tb_inventory_inventories')
                ->onUpdate('restrict')
                ->onDelete('cascade');

            $table->foreign('supplier_id')->references('id')->on('tb_inventory_suppliers')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tb_inventory_inventory_suppliers');
        Schema::dropIfExists('tb_inventory_suppliers');
    }
}
