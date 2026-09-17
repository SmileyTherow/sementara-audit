<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvest_id')
                ->constrained('harvests')
                ->cascadeOnDelete();
            $table->decimal('available_quantity', 15, 2);
            $table->decimal('reserved_quantity', 15, 2)->default(0);
            $table->decimal('sold_quantity', 15, 2)->default(0);
            $table->string('unit', 50);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->string('status', 50)->default('available');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_availabilities');
    }
};
