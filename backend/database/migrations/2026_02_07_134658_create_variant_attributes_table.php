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
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Size", "Color", "Material"
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value'); // e.g., "Small", "Red", "Cotton"
            $table->string('display_value')->nullable(); // For display purposes
            $table->string('color_code')->nullable(); // For color variants (hex code)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['variant_attribute_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('variant_attributes');
    }
};
