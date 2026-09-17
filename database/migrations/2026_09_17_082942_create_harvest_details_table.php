<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvest_id')
                ->constrained('harvests')
                ->cascadeOnDelete();
            $table->string('product_name', 150)->nullable();
            $table->decimal('quantity', 15, 2);
            $table->string('unit', 50);
            $table->string('quality', 50)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_details');
    }
};
