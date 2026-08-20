<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->json('antecedent');         // e.g. ["Lipstick"]
            $table->json('consequent');          // e.g. ["Foundation"]
            $table->double('support', 8, 6);     // support value
            $table->double('confidence', 8, 6);  // confidence value
            $table->double('lift', 8, 6);        // lift value
            $table->integer('antecedent_count'); // how many transactions contain antecedent
            $table->integer('consequent_count'); // how many transactions contain consequent
            $table->integer('both_count');       // how many transactions contain both
            $table->integer('total_transactions');
            $table->double('min_support', 8, 6);
            $table->double('min_confidence', 8, 6);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('association_rules');
    }
};
