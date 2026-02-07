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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Shipping", "Express Shipping"
            $table->string('code')->unique(); // e.g., "standard", "express"
            $table->string('type')->default('fixed'); // fixed, weight_based, price_based
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->decimal('cost_per_kg', 10, 2)->nullable(); // For weight_based
            $table->decimal('cost_per_item', 10, 2)->nullable(); // For item-based
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Free shipping above this amount
            $table->integer('estimated_days_min')->nullable();
            $table->integer('estimated_days_max')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
