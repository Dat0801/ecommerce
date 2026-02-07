<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name')->nullable(); // e.g., "Small - Red"
            $table->decimal('price', 10, 2)->nullable(); // Override base price if different
            $table->integer('stock')->default(0);
            $table->string('image')->nullable(); // Variant-specific image
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['product_id', 'is_default']);
        });

        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_attribute_value_id')->constrained('variant_attribute_values')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['product_variant_id', 'variant_attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_values');
        Schema::dropIfExists('product_variants');
    }
};
