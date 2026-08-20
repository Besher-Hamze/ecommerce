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
        Schema::table('shop_products', function (Blueprint $table) {
            $table->integer('count');
			$table->tinyinteger('shipping')->default('0');
			$table->tinyinteger('status')->default('0');
			$table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('product_states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            //
        });
    }
};
