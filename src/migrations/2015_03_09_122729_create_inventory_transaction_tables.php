<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTransactionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tb_inventory_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('stock_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('state');
            $table->decimal('quantity', 10, 2)->default(0);

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->foreign('stock_id')->references('id')->on('tb_inventory_stocks')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });

        Schema::create('tb_inventory_transaction_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('transaction_id')->unsigned();

            /*
             * Allows tracking states for each transaction
             */
            $table->string('state_before');
            $table->string('state_after');

            /*
             * Allows tracking the quantities of each transaction
             */
            $table->string('quantity_before');
            $table->string('quantity_after');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->foreign('transaction_id')->references('id')->on('tb_inventory_transactions')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tb_inventory_transaction_histories');
        Schema::dropIfExists('tb_inventory_transactions');
    }
}
