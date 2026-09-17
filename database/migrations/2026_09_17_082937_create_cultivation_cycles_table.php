<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultivation_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kwt_id')
                ->constrained('kwts')
                ->cascadeOnDelete();
            $table->foreignId('commodity_id')
                ->constrained('commodities')
                ->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->date('planting_date');
            $table->date('estimated_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('planting_area', 15, 2)->nullable();
            $table->decimal('seed_quantity', 15, 2)->nullable();
            $table->decimal('target_quantity', 15, 2)->nullable();
            $table->string('target_unit', 50)->nullable();
            $table->string('status', 50)->default('planned');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('kwt_id');
            $table->index('commodity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultivation_cycles');
    }
};
