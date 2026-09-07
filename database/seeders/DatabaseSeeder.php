<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\Kwt;
use App\Models\Post;
use App\Models\ProcessedProduct;
use App\Models\ProcessedProductCategory;
use App\Models\Setting;
use App\Models\User;
use App\Services\CultivationService;
use App\Services\HarvestCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::where('email', 'superadmin@kwt.test')->exists()) {
            return;
        }
        foreach (['cooperative_name' => 'Forum/Koperasi KWT Kota Tangerang', 'cooperative_whatsapp' => '', 'cooperative_phone' => '', 'cooperative_email' => 'koperasi@kwt.test', 'cooperative_address' => 'Kota Tangerang, Banten', 'site_name' => 'KWT Tangerang', 'site_description' => 'Pantau budidaya, jadwal panen, dan hasil kebun Kelompok Wanita Tani Kota Tangerang.', 'near_harvest_days' => '7', 'default_preorder_days' => '14', 'site_logo' => '', 'site_favicon' => ''] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
        $password = 'KwtDemo2026!';
        User::create(['name' => 'Super Admin', 'email' => 'superadmin@kwt.test', 'password' => $password, 'role' => 'super_admin']);
        User::create(['name' => 'Admin Koperasi', 'email' => 'koperasi@kwt.test', 'password' => $password, 'role' => 'admin_koperasi']);
        $category = CommodityCategory::create(['name' => 'Sayur & Buah']);
        $processedCategory = ProcessedProductCategory::create(['name' => 'Olahan Kebun']);
        $cultivation = app(CultivationService::class);
        $harvestService = app(HarvestCalculationService::class);
        foreach (['Mawar', 'Melati', 'Anggrek'] as $i => $name) {
            $kwt = Kwt::create(['code' => 'KWT-00'.($i + 1), 'name' => 'KWT '.$name, 'slug' => 'kwt-'.strtolower($name), 'description' => 'Bersama perempuan di lingkungan sekitar, KWT '.$name.' merawat lahan menjadi kebun produktif. Kami menanam sayuran dan buah, berbagi pengetahuan, serta mengolah hasil kebun untuk kesejahteraan bersama.', 'address' => 'Kebun Komunitas '.$name.', Kota Tangerang', 'district' => ['Cipondoh', 'Karawaci', 'Cibodas'][$i], 'village' => ['Cipondoh Indah', 'Karawaci Baru', 'Jatiuwung'][$i], 'chairman_name' => ['Ibu Siti Aminah', 'Ibu Ratna Sari', 'Ibu Sri Wahyuni'][$i], 'established_year' => 2018 + $i, 'status' => 'active']);
            $user = User::create(['name' => 'Pengelola '.$name, 'email' => strtolower($name).'@kwt.test', 'password' => $password, 'role' => 'admin_kwt', 'kwt_id' => $kwt->id]);
            $names = [['Jeruk Peras', 'Pakcoy'], ['Cabai Rawit', 'Selada'], ['Kangkung', 'Pakcoy']][$i];
            foreach ($names as $j => $commodityName) {
                $commodity = Commodity::create(['kwt_id' => $kwt->id, 'category_id' => $category->id, 'name' => $commodityName, 'slug' => Str::slug($commodityName.'-'.$name), 'description' => $commodityName.' hasil kebun komunitas '.$kwt->name.'. Dirawat bersama dan dipantau pertumbuhannya secara berkala.', 'variety' => $commodityName === 'Jeruk Peras' ? 'Jeruk lokal' : 'Varietas kebun lokal', 'default_growing_days' => $commodityName === 'Jeruk Peras' ? 90 : 35, 'default_survival_rate' => 90, 'default_expected_weight_per_plant' => $commodityName === 'Jeruk Peras' ? 0.3 : 0.25, 'yield_unit' => $commodityName === 'Kangkung' ? 'ikat' : 'kg', 'default_seed_reserve_percentage' => 10, 'cultivation_method' => 'Kebun pekarangan', 'is_active' => true]);
                $ready = $i === 0 && $j === 0;
                $near = ($i === 1 && $j === 0) || ($i === 2 && $j === 0);
                $start = now()->startOfDay()->subDays($ready ? 95 : ($near ? 31 : 7));
                $cycle = $cultivation->create(['commodity_id' => $commodity->id, 'start_date' => $start->toDateString(), 'expected_harvest_date' => $ready ? now()->subDays(5)->toDateString() : now()->addDays($near ? 4 : 28)->toDateString(), 'seed_quantity' => 1000, 'planting_quantity' => 1000, 'seed_unit' => 'bibit', 'status' => $near ? 'near_harvest' : 'growing', 'preorder_enabled' => $i === 1 && $j === 0, 'person_in_charge' => $user->name, 'location_name' => 'Kebun '.$kwt->name], $user);
                foreach ([1, 5] as $day) {
                    $monitor = $cultivation->monitor($cycle, ['monitoring_date' => $start->copy()->addDays($day)->toDateString(), 'healthy_plants' => $ready ? 870 : 920, 'alive_plants' => 950, 'dead_plants' => 50, 'damaged_plants' => 20, 'current_estimated_weight_per_plant' => $ready ? 0.29 : 0.24, 'average_height' => 12 + $day, 'height_unit' => 'cm', 'condition' => 'Daun tumbuh baik', 'treatment' => 'Penyiraman pagi dan sore', 'fertilizer_notes' => 'Pupuk kompos kebun', 'general_notes' => 'Pengecekan rutin bersama anggota KWT.', 'is_public' => true], [], $user);
                    if (function_exists('imagecreatetruecolor')) {
                        $im = imagecreatetruecolor(640, 400);
                        $bg = imagecolorallocate($im, 226, 235, 207);
                        imagefill($im, 0, 0, $bg);
                        $green = imagecolorallocate($im, 65, 109, 64);
                        $light = imagecolorallocate($im, 133, 166, 89);
                        for ($x = 40; $x < 640; $x += 80) {
                            for ($y = 150; $y < 400; $y += 95) {
                                imagefilledellipse($im, $x - 12, $y - 15, 35, 55, $green);
                                imagefilledellipse($im, $x + 12, $y - 24, 30, 55, $light);
                            }
                        }
                        imagestring($im, 5, 24, 25, 'DEMO - Dokumentasi hari '.$day, $green);
                        ob_start();
                        imagepng($im);
                        $bytes = ob_get_clean();
                        imagedestroy($im);
                        $path = "kwts/{$kwt->id}/cultivation/{$cycle->id}/monitoring/{$monitor->id}/demo.png";
                        Storage::disk('local')->put($path, $bytes);
                        $monitor->photos()->create(['file_path' => $path, 'caption' => 'Ilustrasi demo perkembangan tanaman', 'is_public' => true, 'uploaded_by' => $user->id, 'taken_at' => $monitor->monitoring_date]);
                    }
                }
                if ($ready) {
                    foreach ([140, 100] as $n => $amount) {
                        $harvestService->create($cycle, ['harvest_date' => now()->subDays(4 - $n)->toDateString(), 'gross_quantity' => $amount + 5, 'usable_quantity' => $amount, 'damaged_quantity' => 5, 'harvested_plants' => 400, 'notes' => 'Panen demo bertahap', 'details' => [['grade' => 'Grade A', 'quantity' => $amount, 'unit' => 'kg']]], [], $user);
                    }
                    $harvestService->availability($commodity, ['available_quantity' => 115, 'availability_status' => 'available', 'notes' => 'Sisa aktual setelah penyaluran offline (data demo)'], $user, $cycle);
                }
            }
            ProcessedProduct::create(['kwt_id' => $kwt->id, 'category_id' => $processedCategory->id, 'name' => ['Sambal Kebun Mawar', 'Jamu Kunyit Asam', 'Keripik Bayam'][$i], 'slug' => 'olahan-'.strtolower($name), 'description' => 'Kreasi rumahan anggota '.$kwt->name.', diolah dari bahan pilihan kebun lokal.', 'price' => [25000, 15000, 18000][$i], 'unit' => 'pcs', 'availability_status' => 'available', 'available_quantity' => 20, 'is_active' => true]);
            Post::create(['kwt_id' => $kwt->id, 'title' => ['Panen bersama, berbagi kebaikan dari kebun Mawar', 'Belajar kompos: dari dapur kembali ke kebun', 'Pagi yang hijau bersama perempuan Anggrek'][$i], 'slug' => 'cerita-'.strtolower($name), 'excerpt' => 'Cerita kebersamaan anggota KWT dalam merawat kebun dan lingkungan.', 'content' => 'Anggota '.$kwt->name.' berkumpul untuk merawat tanaman dan berbagi pengetahuan tentang budidaya di pekarangan. Kegiatan ini menjadi kesempatan untuk belajar bersama serta memperkuat semangat gotong royong.\n\nData dan cerita ini merupakan contoh demonstrasi aplikasi.', 'status' => 'published', 'published_at' => now()->subDays($i + 2), 'created_by' => $user->id]);
        }
    }
}
