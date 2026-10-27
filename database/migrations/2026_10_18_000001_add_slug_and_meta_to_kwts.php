<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kwts', function (Blueprint $table) {
            // Tambah kolom slug (nullable agar tidak memaksa migrasi pada data existing)
            $table->string('slug', 150)->nullable()->unique()->after('code');

            // Tambah beberapa kolom meta yang dipakai oleh seed/test
            $table->text('description')->nullable()->after('name');
            $table->string('chairman_name', 150)->nullable()->after('leader_name');
            $table->integer('established_year')->nullable()->after('chairman_name');
            $table->string('status', 50)->default('active')->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('kwts', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description', 'chairman_name', 'established_year', 'status']);
        });
    }
};
