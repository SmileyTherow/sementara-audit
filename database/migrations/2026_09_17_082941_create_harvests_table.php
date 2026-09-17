<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivation_cycle_id')
                ->constrained('cultivation_cycles')
                ->cascadeOnDelete();
            $table->date('harvest_date');
            $table->decimal('predicted_quantity', 15, 2)->nullable();
            $table->decimal('realized_quantity', 15, 2)->nullable();
            $table->decimal('variance_quantity', 15, 2)->nullable();
            $table->string('quality_grade', 50)->nullable();
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('verified_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('cultivation_cycle_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
