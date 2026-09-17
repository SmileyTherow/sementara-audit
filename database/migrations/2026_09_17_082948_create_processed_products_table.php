<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processed_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processed_product_category_id')
                ->constrained('processed_product_categories')
                ->cascadeOnDelete();
            $table->foreignId('kwt_id')->nullable()
                ->constrained('kwts')
                ->nullOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('stock', 15, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_products');
    }
};
