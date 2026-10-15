<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });
        foreach (['commodities', 'processed_products'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                for ($slot = 2; $slot <= 5; $slot++) {
                    $table->string('image_path_' . $slot)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['commodities', 'processed_products'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn(['image_path_2', 'image_path_3', 'image_path_4', 'image_path_5']);
            });
        }
        Schema::table('commodities', fn (Blueprint $table) => $table->dropColumn('image_path'));
    }
};
