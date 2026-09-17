<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('growth_monitoring_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('growth_monitoring_id')
                ->constrained('growth_monitorings')
                ->cascadeOnDelete();
            $table->string('file_path', 255);
            $table->string('caption', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('growth_monitoring_photos');
    }
};
