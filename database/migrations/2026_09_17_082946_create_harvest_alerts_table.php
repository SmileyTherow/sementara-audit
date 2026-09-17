<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivation_cycle_id')->nullable()
                ->constrained('cultivation_cycles')
                ->nullOnDelete();
            $table->foreignId('harvest_id')->nullable()
                ->constrained('harvests')
                ->nullOnDelete();
            $table->string('alert_type', 50);
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->string('channel', 50)->default('dashboard');
            $table->foreignId('recipient_user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status', 50)->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index('alert_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_alerts');
    }
};
