<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_category_id')
                ->constrained('commodity_categories')
                ->cascadeOnDelete();
            $table->string('code', 50)->nullable()->unique();
            $table->string('name', 150);
            $table->string('default_unit', 50)->nullable();
            $table->integer('default_growth_days')->nullable();
            $table->integer('default_harvest_days')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commodities');
    }
};
