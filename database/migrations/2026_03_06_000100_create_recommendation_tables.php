<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Apriori output. Itemsets are stored as jsonb arrays of products.id so that
        // cart-level lookups can use the containment operator: antecedent <@ '[cart ids]'.
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->string('level', 10)->default('product')->comment('product | type');
            $table->jsonb('antecedent');
            $table->jsonb('consequent');
            $table->smallInteger('antecedent_size')->default(1);
            $table->double('support');
            $table->double('confidence');
            $table->double('lift');
            $table->double('leverage')->default(0);
            $table->double('conviction')->nullable();
            $table->integer('antecedent_count');
            $table->integer('consequent_count');
            $table->integer('both_count');
            $table->integer('total_transactions');
            $table->double('min_support');
            $table->double('min_confidence');
            $table->timestamps();

            $table->index(['level', 'lift']);
        });
        DB::statement('CREATE INDEX association_rules_antecedent_gin ON association_rules USING GIN (antecedent jsonb_path_ops)');

        // Pre-computed (offline) recommendations served online with a single indexed query.
        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('cross_sell | upsell | similar | cf');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('recommended_product_id')->constrained('products')->cascadeOnDelete();
            $table->double('score');
            $table->smallInteger('rank');
            $table->jsonb('meta')->nullable();

            $table->unique(['type', 'product_id', 'recommended_product_id']);
            $table->index(['type', 'product_id', 'rank']);
        });

        // Implicit feedback (browsing history) used by collaborative filtering and "recently viewed".
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 100)->index();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('source', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
        });

        // Online evaluation: impressions, clicks, add-to-cart and purchases per recommendation widget.
        Schema::create('recommendation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->string('event', 20)->comment('impression | click | add_to_cart | purchase');
            $table->string('source', 20)->comment('cross_sell | upsell | cf | similar | trending | bundle');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('context_product_id')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['source', 'event']);
            $table->index('created_at');
        });

        Schema::create('recommendation_runs', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('train')->comment('train | evaluate');
            $table->string('status', 20)->default('completed');
            $table->jsonb('params')->nullable();
            $table->jsonb('stats')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recommendation_runs');
        Schema::dropIfExists('recommendation_events');
        Schema::dropIfExists('product_views');
        Schema::dropIfExists('product_recommendations');
        Schema::dropIfExists('association_rules');
    }
};
