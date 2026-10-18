<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('eng_name');
            $table->string('slug')->nullable()->unique();
            $table->string('icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('eng_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('eng_meta_title')->nullable();
            $table->string('eng_meta_keyword')->nullable();
            $table->text('eng_meta_description')->nullable();
            $table->string('image_url')->nullable();
            $table->smallInteger('status')->default(1)->comment('1=visible in store navigation');
            $table->integer('count_prod')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('image')->nullable();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('eng_name')->nullable();
            $table->text('description')->nullable();
            $table->text('eng_description')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->smallInteger('status')->default(1);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('product_states', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('source_id')->nullable()->unique()->comment('id in the Makeup API dataset');
            $table->string('name');
            $table->string('eng_name');
            $table->text('description')->nullable();
            $table->text('eng_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('eng_meta_title')->nullable();
            $table->string('eng_meta_keyword')->nullable();
            $table->text('eng_meta_description')->nullable();
            $table->string('product_code')->nullable()->index();
            $table->string('image_url')->nullable();
            $table->string('form', 40)->nullable()->comment('liquid, pencil, powder, palette ...');
            $table->jsonb('tags')->default('[]');
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });

        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('product_states')->nullOnDelete();
            $table->decimal('price', 10, 2);
            $table->integer('count')->default(0);
            $table->smallInteger('shipping')->default(0);
            $table->smallInteger('status')->default(0);
            $table->string('product_type', 40)->nullable()->index();
            $table->text('product_colors')->nullable();
            $table->timestamps();

            $table->index('price');
        });

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index()->comment('shop_products.id');
            $table->string('url');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('shop_products');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_states');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
