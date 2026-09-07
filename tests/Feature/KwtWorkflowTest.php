<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Kwt;
use App\Models\Post;
use App\Models\ProcessedProduct;
use App\Models\Setting;
use App\Models\User;
use App\Services\CultivationCalculationService;
use App\Services\CultivationService;
use App\Services\HarvestCalculationService;
use App\Services\HarvestReportService;
use App\Services\WhatsappMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KwtWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $other;

    private Commodity $commodity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $kwt = Kwt::create(['name' => 'KWT Mawar', 'code' => 'M', 'slug' => 'mawar', 'status' => 'active']);
        $other = Kwt::create(['name' => 'KWT Melati', 'code' => 'L', 'slug' => 'melati', 'status' => 'active']);
        $this->owner = User::create(['name' => 'Mawar', 'email' => 'mawar@test.test', 'password' => 'KwtDemo2026!', 'role' => 'admin_kwt', 'kwt_id' => $kwt->id]);
        $this->other = User::create(['name' => 'Melati', 'email' => 'melati@test.test', 'password' => 'KwtDemo2026!', 'role' => 'admin_kwt', 'kwt_id' => $other->id]);
        $this->commodity = Commodity::create(['kwt_id' => $kwt->id, 'name' => 'Jeruk Peras', 'slug' => 'jeruk', 'default_growing_days' => 90, 'default_survival_rate' => 90, 'default_expected_weight_per_plant' => .3, 'default_seed_reserve_percentage' => 10, 'yield_unit' => 'kg', 'is_active' => true]);
        Setting::create(['key' => 'cooperative_whatsapp', 'value' => '081234567890']);
        Setting::create(['key' => 'cooperative_name', 'value' => 'Koperasi KWT']);
    }

    private function cycle(array $extra = []): CultivationCycle
    {
        return app(CultivationService::class)->create(array_replace(['commodity_id' => $this->commodity->id, 'start_date' => now()->subDays(95)->toDateString(), 'seed_quantity' => 1000, 'planting_quantity' => 1000, 'status' => 'growing'], $extra), $this->owner);
    }

    public function test_login_and_inactive_account(): void
    {
        $this->post('/login', ['email' => $this->owner->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $this->owner->email, 'password' => 'KwtDemo2026!'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->owner);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->owner->update(['is_active' => false]);
        $this->post('/login', ['email' => $this->owner->email, 'password' => 'KwtDemo2026!'])->assertSessionHasErrors('email');
    }

    public function test_kwt_isolation_on_pages_writes_and_reports(): void
    {
        $cycle = $this->cycle();
        $this->actingAs($this->other);
        $this->get('/admin/cycles/'.$cycle->id)->assertForbidden();
        $this->get('/admin/manage/commodities/'.$this->commodity->id.'/edit')->assertForbidden();
        $this->delete('/admin/manage/commodities/'.$this->commodity->id)->assertForbidden();
        $this->post('/admin/cycles/'.$cycle->id.'/monitorings', [])->assertForbidden();
        $this->post('/admin/cycles/'.$cycle->id.'/harvests', [])->assertForbidden();
        $this->post('/admin/commodities/'.$this->commodity->id.'/availability', ['available_quantity' => 1, 'availability_status' => 'available'])->assertForbidden();
        $this->get('/admin/manage/commodities')->assertOk()->assertDontSee('Jeruk Peras');
        $report = app(HarvestReportService::class)->build(['start_date' => now()->subYear()->toDateString(), 'end_date' => now()->addYear()->toDateString(), 'kwt_id' => $this->owner->kwt_id], $this->other);
        $this->assertCount(0, $report['rows']);
        $this->get('/admin/manage/users')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
    }

    public function test_commodity_and_cycle_creation_calculations(): void
    {
        $this->actingAs($this->owner)->post('/admin/manage/commodities', ['kwt_id' => $this->other->kwt_id, 'name' => 'Pakcoy', 'default_growing_days' => 35, 'default_survival_rate' => 90, 'default_expected_weight_per_plant' => .25, 'yield_unit' => 'kg', 'default_seed_reserve_percentage' => 10, 'is_active' => 1])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('commodities', ['name' => 'Pakcoy', 'kwt_id' => $this->owner->kwt_id]);
        $this->post('/admin/manage/cycles', ['commodity_id' => $this->commodity->id, 'start_date' => now()->toDateString(), 'planting_holes' => 1000, 'reserve_seed_percentage' => 10, 'target_survival_rate' => 90, 'target_weight_per_plant' => .25, 'seed_unit' => 'bibit', 'status' => 'growing', 'preorder_enabled' => 0])->assertSessionHasNoErrors()->assertRedirect();
        $cycle = CultivationCycle::first();
        $this->assertEquals(1100, $cycle->seed_quantity);
        $this->assertEquals(100, $cycle->reserve_seed_quantity);
        $this->assertEquals(900, $cycle->target_productive_plants);
        $this->assertEquals(225, $cycle->initial_expected_yield);
        $data = app(CultivationCalculationService::class)->calculate(['start_date' => '2026-09-01', 'seed_quantity' => 1000], $this->commodity);
        $this->assertEquals(270, $data['initial_expected_yield']);
    }

    public function test_foreign_commodity_and_invalid_input_rejected(): void
    {
        $this->actingAs($this->other)->post('/admin/manage/cycles', ['commodity_id' => $this->commodity->id, 'start_date' => now()->toDateString(), 'seed_quantity' => 1000, 'seed_unit' => 'bibit', 'status' => 'growing', 'preorder_enabled' => 0])->assertSessionHasErrors('commodity_id');
        $this->actingAs($this->owner)->post('/admin/manage/cycles', ['commodity_id' => $this->commodity->id, 'start_date' => now()->toDateString(), 'expected_harvest_date' => now()->subDay()->toDateString(), 'seed_quantity' => -1, 'target_survival_rate' => 120, 'seed_unit' => 'bibit', 'status' => 'growing', 'preorder_enabled' => 0])->assertSessionHasErrors(['seed_quantity', 'target_survival_rate', 'expected_harvest_date']);
    }

    public function test_monitoring_projection_photos_and_backdated_entries(): void
    {
        Storage::fake('local');
        $cycle = $this->cycle();
        $this->actingAs($this->owner);
        $base = ['monitoring_date' => now()->subDay()->toDateString(), 'healthy_plants' => 870, 'alive_plants' => 900, 'dead_plants' => 100, 'height_unit' => 'cm', 'current_estimated_weight_per_plant' => .29, 'is_public' => 0, 'photos' => [UploadedFile::fake()->image('growth.jpg')]];
        $this->post('/admin/cycles/'.$cycle->id.'/monitorings', $base)->assertSessionHasNoErrors()->assertRedirect();
        $cycle->refresh();
        $this->assertEquals(252.3, $cycle->latest_projected_yield);
        $this->assertEquals(270, $cycle->initial_expected_yield);
        $photo = $cycle->monitorings()->first()->photos()->first();
        Storage::disk('local')->assertExists($photo->file_path);
        $this->actingAs($this->other)->get('/foto/monitoring/'.$photo->id)->assertForbidden();
        $this->actingAs($this->owner)->get('/foto/monitoring/'.$photo->id)->assertOk();
        $photo->update(['is_public' => true]);
        auth()->logout();
        $this->get('/foto/monitoring/'.$photo->id)->assertOk();
        $this->actingAs($this->owner)->post('/admin/cycles/'.$cycle->id.'/monitorings', array_replace($base, ['monitoring_date' => now()->subDays(20)->toDateString(), 'healthy_plants' => 800, 'photos' => []]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(252.3, $cycle->fresh()->latest_projected_yield);
        $this->post('/admin/cycles/'.$cycle->id.'/monitorings', array_replace($base, ['healthy_plants' => 1001, 'photos' => []]))->assertSessionHasErrors('healthy_plants');
        $this->post('/admin/cycles/'.$cycle->id.'/monitorings', array_replace($base, ['photos' => [UploadedFile::fake()->create('script.php', 1, 'application/x-php')]]))->assertSessionHasErrors('photos.0');
    }

    public function test_multiple_harvests_actual_yield_availability_and_correction(): void
    {
        $cycle = $this->cycle();
        $this->actingAs($this->owner);
        foreach ([140, 100] as $amount) {
            $this->post('/admin/cycles/'.$cycle->id.'/harvests', ['harvest_date' => now()->toDateString(), 'gross_quantity' => $amount + 5, 'usable_quantity' => $amount, 'damaged_quantity' => 5])->assertSessionHasNoErrors()->assertRedirect();
        }
        $this->assertEquals(240, $cycle->fresh()->actual_yield);
        $this->assertEquals(2, $cycle->harvests()->count());
        $this->assertEquals(240, $this->commodity->fresh()->public_state['quantity']);
        $this->post('/admin/commodities/'.$this->commodity->id.'/availability', ['available_quantity' => 115, 'availability_status' => 'limited'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(115, $this->commodity->fresh()->public_state['quantity']);
        $this->assertSame('Ready', $this->commodity->fresh()->public_state['status']);
        $this->delete('/admin/harvests/'.$cycle->harvests()->first()->id)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(100, $cycle->fresh()->actual_yield);
        $this->assertEquals(0, $this->commodity->fresh()->public_state['quantity']);
    }

    public function test_public_status_preorder_and_whatsapp(): void
    {
        $this->assertSame('Tidak Aktif', $this->commodity->public_state['status']);
        $cycle = $this->cycle(['expected_harvest_date' => now()->addDays(30)->toDateString()]);
        $this->assertSame('Sedang Ditanam', $this->commodity->fresh()->public_state['status']);
        $cycle->update(['expected_harvest_date' => now()->addDays(3)]);
        $this->assertSame('Segera Panen', $this->commodity->fresh()->public_state['status']);
        $cycle->update(['preorder_enabled' => true]);
        $state = $this->commodity->fresh()->public_state;
        $this->assertSame('Pre-Order', $state['status']);
        $link = app(WhatsappMessageService::class)->buildPreorderMessage($this->commodity, 10, $state);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $link);
        $this->assertStringContainsString('10 kg', rawurldecode($link));
        $this->get('/whatsapp/commodity/'.$this->commodity->id.'?quantity=10')->assertRedirect($link);
        $cycle->update(['status' => 'completed']);
        $this->get('/komoditas/jeruk')->assertNotFound();
    }

    public function test_report_groups_cycles_once_and_separates_units(): void
    {
        $cycle = $this->cycle(['expected_harvest_date' => now()->toDateString()]);
        $service = app(HarvestCalculationService::class);
        foreach ([35, 42] as $amount) {
            $service->create($cycle, ['harvest_date' => now()->toDateString(), 'gross_quantity' => $amount, 'usable_quantity' => $amount], [], $this->owner);
        }
        $other = Commodity::create(['kwt_id' => $this->owner->kwt_id, 'name' => 'Kangkung', 'slug' => 'kangkung', 'default_growing_days' => 30, 'default_survival_rate' => 100, 'default_expected_weight_per_plant' => 1, 'default_seed_reserve_percentage' => 0, 'yield_unit' => 'ikat']);
        $second = $this->cycle(['commodity_id' => $other->id, 'expected_harvest_date' => now()->toDateString()]);
        $service->create($second, ['harvest_date' => now()->toDateString(), 'gross_quantity' => 10, 'usable_quantity' => 10], [], $this->owner);
        $report = app(HarvestReportService::class)->build([], $this->owner);
        $this->assertCount(2, $report['rows']);
        $this->assertEquals(270, $report['totalsByUnit']['kg']['expected']);
        $this->assertEquals(77, $report['totalsByUnit']['kg']['actual']);
        $this->assertEquals(10, $report['totalsByUnit']['ikat']['actual']);
        $this->actingAs($this->owner)->get('/admin/reports')->assertOk()->assertSee('Subtotal KWT Mawar');
        $response = $this->get('/admin/reports?export=xlsx')->assertOk()->assertDownload();
        $zip = new \ZipArchive;
        $zip->open($response->baseResponse->getFile()->getPathname());
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('<v>77</v>', $sheet);
        $zip->close();
    }

    public function test_cooperative_read_only_and_special_permission(): void
    {
        $cycle = $this->cycle();
        $coop = User::create(['name' => 'Koperasi', 'email' => 'coop@test.test', 'password' => 'KwtDemo2026!', 'role' => 'admin_koperasi']);
        $this->actingAs($coop)->get('/admin/cycles/'.$cycle->id)->assertOk();
        $this->post('/admin/cycles/'.$cycle->id.'/harvests', [])->assertForbidden();
        $this->post('/admin/commodities/'.$this->commodity->id.'/availability', ['available_quantity' => 10, 'availability_status' => 'available'])->assertSessionHasNoErrors()->assertRedirect();
        $coop->update(['can_manage_cultivation' => true]);
        $this->post('/admin/cycles/'.$cycle->id.'/harvests', ['harvest_date' => now()->toDateString(), 'gross_quantity' => 10, 'usable_quantity' => 10])->assertSessionHasNoErrors()->assertRedirect();
    }

    public function test_cycle_update_preserves_initial_values(): void
    {
        $cycle = $this->cycle();
        $this->actingAs($this->owner)->put('/admin/manage/cycles/'.$cycle->id, ['status' => 'completed', 'status_reason' => 'Panen selesai', 'preorder_enabled' => 0, 'initial_expected_yield' => 1])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(270, $cycle->fresh()->initial_expected_yield);
        $this->assertSame('completed', $cycle->fresh()->status);
    }

    public function test_all_public_and_admin_pages_render(): void
    {
        $cycle = $this->cycle();
        foreach (['/', '/tentang', '/kontak', '/kwt', '/kwt/mawar', '/komoditas', '/komoditas/jeruk', '/jadwal-panen', '/produk-olahan', '/kegiatan', '/login'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->actingAs($this->owner);
        foreach (['/admin', '/admin/account', '/admin/manage/kwts', '/admin/manage/commodities', '/admin/manage/commodities/create', '/admin/manage/cycles', '/admin/manage/cycles/create', '/admin/manage/cycles/'.$cycle->id.'/edit', '/admin/cycles/'.$cycle->id, '/admin/monitorings', '/admin/harvests', '/admin/manage/products', '/admin/manage/posts', '/admin/manage/galleries', '/admin/reports'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_super_admin_management_and_published_content(): void
    {
        Storage::fake('public');
        $super = User::create(['name' => 'Super', 'email' => 'super@test.test', 'password' => 'KwtDemo2026!', 'role' => 'super_admin']);
        $this->actingAs($super);
        foreach (['/admin/settings', '/admin/manage/users', '/admin/manage/users/create', '/admin/manage/kwts/create', '/admin/manage/products/create', '/admin/manage/posts/create', '/admin/manage/galleries/create', '/admin/manage/commodity-categories', '/admin/manage/product-categories'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->post('/admin/manage/users', ['name' => 'Pengelola baru', 'email' => 'new@test.test', 'password' => 'VerySecure2026!', 'role' => 'admin_kwt', 'kwt_id' => $this->owner->kwt_id, 'is_active' => 1, 'can_manage_cultivation' => 0])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'new@test.test', 'role' => 'admin_kwt']);
        $this->post('/admin/manage/posts', ['title' => 'Kegiatan Publik', 'kwt_id' => $this->owner->kwt_id, 'content' => '<script>alert(1)</script>', 'status' => 'published', 'cover_image' => UploadedFile::fake()->image('cover.jpg')])->assertSessionHasNoErrors()->assertRedirect();
        $post = Post::first();
        Storage::disk('public')->assertExists($post->cover_image);
        $this->get('/kegiatan/'.$post->slug)->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $post->update(['published_at' => now()->addDay()]);
        $this->get('/kegiatan/'.$post->slug)->assertNotFound();
        $this->post('/admin/manage/products', ['kwt_id' => $this->owner->kwt_id, 'name' => 'Jamu', 'description' => 'Jamu kebun', 'unit' => 'pcs', 'availability_status' => 'available', 'available_quantity' => 20, 'is_active' => 1])->assertSessionHasNoErrors()->assertRedirect();
        $product = ProcessedProduct::first();
        $this->get('/produk-olahan/'.$product->slug)->assertOk();
        $this->get('/whatsapp/product/'.$product->id.'?quantity=2')->assertRedirect();
        $this->post('/admin/manage/galleries', ['title' => 'Kebun', 'kwt_id' => $this->owner->kwt_id, 'image' => UploadedFile::fake()->image('garden.png'), 'published_at' => now()->toDateString()])->assertSessionHasNoErrors()->assertRedirect();
        $this->get('/kwt/mawar')->assertOk();
        $this->post('/admin/settings', ['cooperative_name' => 'Koperasi Baru', 'cooperative_whatsapp' => '0895333212323', 'site_name' => 'KWT Tangerang', 'default_preorder_days' => 14, 'near_harvest_days' => 7])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('0895333212323', Setting::valueOf('cooperative_whatsapp'));
        $this->get('/admin/manage/commodities?q[]=bad')->assertSessionHasErrors('q');
    }

    public function test_invalid_photo_date_and_harvest_quantities_leave_no_records(): void
    {
        $cycle = $this->cycle();
        $this->actingAs($this->owner);
        $this->post('/admin/cycles/'.$cycle->id.'/harvests', ['harvest_date' => now()->toDateString(), 'gross_quantity' => 10, 'usable_quantity' => 9, 'damaged_quantity' => 3])->assertSessionHasErrors('usable_quantity');
        $this->post('/admin/cycles/'.$cycle->id.'/harvests', ['harvest_date' => $cycle->start_date->copy()->subDay()->toDateString(), 'gross_quantity' => 10, 'usable_quantity' => 10])->assertSessionHasErrors('harvest_date');
        $this->assertDatabaseCount('harvests', 0);
        $this->post('/admin/cycles/'.$cycle->id.'/monitorings', ['monitoring_date' => $cycle->start_date->copy()->subDay()->toDateString(), 'healthy_plants' => 800, 'current_estimated_weight_per_plant' => .25, 'height_unit' => 'cm', 'is_public' => 0])->assertSessionHasErrors('monitoring_date');
        $this->assertDatabaseCount('growth_monitorings',0);
        $this->delete('/admin/manage/kwts/'.$this->owner->kwt_id)->assertForbidden();
    }
}
