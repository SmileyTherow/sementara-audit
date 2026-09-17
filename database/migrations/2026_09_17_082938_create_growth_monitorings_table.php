<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('growth_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivation_cycle_id')
                ->constrained('cultivation_cycles')
                ->cascadeOnDelete();
            $table->date('monitoring_date');
            $table->decimal('plant_height', 15, 2)->nullable();
            $table->string('plant_condition', 100)->nullable();
            $table->string('health_status', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index('cultivation_cycle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('growth_monitorings');
    }
};
