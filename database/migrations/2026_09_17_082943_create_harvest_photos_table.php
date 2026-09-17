<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvest_id')
                ->constrained('harvests')
                ->cascadeOnDelete();
            $table->string('file_path', 255);
            $table->string('caption', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_photos');
    }
};
