<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kwts', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->string('slug')->unique();
            foreach (['logo', 'cover_image', 'address', 'district', 'village', 'google_maps_url', 'chairman_name', 'whatsapp', 'email'] as $f) {
                $t->string($f)->nullable();
            }
            $t->text('description')->nullable();
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->unsignedSmallInteger('established_year')->nullable();
            $t->string('status')->default('active')->index();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('kwt_id')->nullable()->constrained();
            $t->string('role')->default('admin_kwt');
            $t->boolean('can_manage_cultivation')->default(false);
            $t->boolean('is_active')->default(true);
        });
        foreach (['commodity_categories', 'processed_product_categories'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->string('name')->unique();
                $t->timestamps();
            });
        }
        Schema::create('commodities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kwt_id')->constrained();
            $t->foreignId('category_id')->nullable()->constrained('commodity_categories');
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('variety')->nullable();
            $t->string('image')->nullable();
            $t->text('description')->nullable();
            $t->unsignedInteger('default_growing_days')->default(30);
            $t->decimal('default_survival_rate', 5, 2)->default(90);
            $t->decimal('default_expected_weight_per_plant', 12, 4)->default(.25);
            $t->string('yield_unit')->default('kg');
            $t->decimal('default_seed_reserve_percentage', 5, 2)->default(10);
            $t->string('cultivation_method')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('cultivation_cycles', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->foreignId('kwt_id')->constrained();
            $t->foreignId('commodity_id')->constrained();
            $t->date('start_date')->index();
            $t->date('expected_harvest_date')->index();
            foreach (['seed_quantity', 'reserve_seed_quantity', 'planting_quantity', 'target_productive_plants'] as $f) {
                $t->unsignedInteger($f)->default(0);
            }
            $t->string('seed_unit')->default('bibit');
            $t->string('yield_unit')->default('kg');
            $t->string('seed_source')->nullable();
            $t->decimal('seed_price', 15, 2)->nullable();
            $t->decimal('seed_total_cost', 15, 2)->nullable();
            $t->decimal('reserve_seed_percentage', 5, 2)->default(10);
            $t->unsignedInteger('planting_holes')->nullable();
            $t->decimal('land_area', 12, 2)->nullable();
            foreach (['land_area_unit', 'location_name', 'cultivation_method', 'person_in_charge'] as $f) {
                $t->string($f)->nullable();
            }
            $t->decimal('target_survival_rate', 5, 2);
            $t->decimal('target_weight_per_plant', 12, 4);
            foreach (['initial_expected_yield', 'latest_projected_yield', 'actual_yield'] as $f) {
                $t->decimal($f, 15, 4)->default(0);
            }
            $t->string('status')->default('growing')->index();
            $t->boolean('preorder_enabled')->default(false);
            $t->text('status_reason')->nullable();
            $t->text('notes')->nullable();
            $t->foreignId('created_by')->constrained('users');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('growth_monitorings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cultivation_cycle_id')->constrained();
            $t->date('monitoring_date')->index();
            $t->unsignedInteger('plant_age_days');
            foreach (['alive_plants', 'dead_plants', 'healthy_plants', 'damaged_plants'] as $f) {
                $t->unsignedInteger($f)->nullable();
            }
            foreach (['average_height', 'current_estimated_weight_per_plant', 'current_survival_rate', 'projected_yield'] as $f) {
                $t->decimal($f, 15, 4)->nullable();
            }
            $t->string('height_unit')->default('cm');
            foreach (['condition', 'treatment', 'fertilizer_notes', 'pest_notes', 'disease_notes', 'general_notes'] as $f) {
                $t->text($f)->nullable();
            }
            $t->foreignId('created_by')->constrained('users');
            $t->timestamps();
        });
        Schema::create('growth_monitoring_photos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('growth_monitoring_id')->constrained()->cascadeOnDelete();
            $t->string('file_path');
            $t->string('thumbnail_path')->nullable();
            $t->string('caption')->nullable();
            $t->dateTime('taken_at')->nullable();
            $t->boolean('is_public')->default(false);
            $t->foreignId('uploaded_by')->constrained('users');
            $t->timestamps();
        });
        Schema::create('harvests', function (Blueprint $t) {
            $t->id();
            $t->string('harvest_code')->unique();
            $t->foreignId('cultivation_cycle_id')->constrained();
            $t->foreignId('kwt_id')->constrained();
            $t->foreignId('commodity_id')->constrained();
            $t->date('harvest_date')->index();
            $t->unsignedInteger('harvest_sequence');
            $t->unsignedInteger('harvested_plants')->nullable();
            foreach (['gross_quantity', 'usable_quantity', 'damaged_quantity', 'average_weight_per_plant'] as $f) {
                $t->decimal($f, 15, 4)->default(0);
            }
            $t->string('yield_unit');
            $t->text('notes')->nullable();
            $t->foreignId('created_by')->constrained('users');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['cultivation_cycle_id', 'harvest_sequence']);
        });
        Schema::create('harvest_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('harvest_id')->constrained();
            $t->string('grade');
            $t->string('size_label')->nullable();
            $t->decimal('quantity', 15, 4);
            $t->decimal('weight', 15, 4)->nullable();
            $t->string('unit');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('harvest_photos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('harvest_id')->constrained();
            $t->string('file_path');
            $t->string('caption')->nullable();
            $t->foreignId('uploaded_by')->constrained('users');
            $t->timestamps();
        });
        Schema::create('harvest_availabilities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kwt_id')->constrained();
            $t->foreignId('commodity_id')->constrained();
            $t->foreignId('cultivation_cycle_id')->nullable()->constrained();
            $t->foreignId('harvest_id')->nullable()->constrained();
            $t->decimal('available_quantity', 15, 4)->default(0);
            $t->string('unit');
            $t->string('availability_status')->index();
            $t->dateTime('availability_updated_at');
            $t->text('notes')->nullable();
            $t->foreignId('updated_by')->constrained('users');
            $t->timestamps();
        });
        Schema::create('processed_products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kwt_id')->constrained();
            $t->foreignId('category_id')->nullable()->constrained('processed_product_categories');
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->decimal('price', 15, 2)->nullable();
            $t->string('unit');
            $t->string('image')->nullable();
            $t->string('availability_status');
            $t->decimal('available_quantity', 15, 4)->nullable();
            $t->text('whatsapp_note')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kwt_id')->nullable()->constrained();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->longText('content');
            $t->string('cover_image')->nullable();
            $t->string('status')->default('draft')->index();
            $t->dateTime('published_at')->nullable();
            $t->foreignId('created_by')->constrained('users');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('galleries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kwt_id')->nullable()->constrained();
            $t->string('title');
            $t->string('image');
            $t->text('caption')->nullable();
            $t->dateTime('published_at')->nullable();
            $t->timestamps();
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained();
            $t->string('action');
            $t->string('model');
            $t->unsignedBigInteger('model_id');
            $t->json('old_values')->nullable();
            $t->json('new_values')->nullable();
            $t->string('ip_address')->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'settings', 'galleries', 'posts', 'processed_products', 'harvest_availabilities', 'harvest_photos', 'harvest_details', 'harvests', 'growth_monitoring_photos', 'growth_monitorings', 'cultivation_cycles', 'commodities', 'processed_product_categories', 'commodity_categories'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('kwt_id');
            $t->dropColumn(['role', 'can_manage_cultivation', 'is_active']);
        });
        Schema::dropIfExists('kwts');
    }
};
