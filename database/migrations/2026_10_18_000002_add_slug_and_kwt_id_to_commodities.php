<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            // slug untuk route/public lookup
            $table->string('slug', 150)->nullable()->unique()->after('name');

            // optional convenience column used by some seeders/tests: kwt_id (nullable)
            $table->foreignId('kwt_id')->nullable()->constrained('kwts')->nullOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            // drop foreign key first if needed by DB driver
            if (Schema::hasColumn('commodities', 'kwt_id')) {
                $table->dropConstrainedForeignId('kwt_id');
            }
            $table->dropColumn('slug');
        });
    }
};
