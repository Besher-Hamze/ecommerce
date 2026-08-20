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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('coupon_type')->default('0')->comment('0=default,1=FirstOrder');
            $table->string('code');
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->integer('min_purchase')->nullable();
            $table->integer('discount');
            $table->tinyInteger('discount_type')->default('0')->comment('0=Fixed,1=percent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
