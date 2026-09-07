<?php

namespace Tests\Feature;

use App\Models\Kwt;
use App\Models\Setting;
use Database\Seeders\KwtSayurDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KwtSayurDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_is_public_and_safe_to_seed_twice(): void
    {
        $this->withoutVite();
        $this->seed(KwtSayurDemoSeeder::class);
        $this->seed(KwtSayurDemoSeeder::class);
        $kwt = Kwt::where('slug', 'kwt-sayur-sejahtera')->firstOrFail();
        $this->assertCount(3, $kwt->commodities);
        $this->assertCount(3, $kwt->cycles);
        $this->assertEquals(549, $kwt->cycles->sum('initial_expected_yield'));
        $this->get('/kwt/kwt-sayur-sejahtera')->assertOk()->assertSee('Selada')->assertSee('Kol')->assertSee('Pakcoy');
        foreach ($kwt->commodities as $commodity) {
            $this->assertSame('Sedang Ditanam', $commodity->public_state['status']);
        }
    }

    public function test_growing_selada_cta_opens_central_whatsapp_with_order_text(): void
    {
        $this->withoutVite();
        $this->seed(KwtSayurDemoSeeder::class);
        Setting::create(['key' => 'cooperative_whatsapp', 'value' => '0895333212323']);
        Setting::create(['key' => 'cooperative_name', 'value' => 'Koperasi Pusat KWT']);
        $commodity = \App\Models\Commodity::where('slug', 'selada-sayur-sejahtera')->firstOrFail();
        $this->get('/komoditas/'.$commodity->slug)->assertOk()->assertSee('Tanyakan Pemesanan')->assertSee('via WhatsApp')->assertSee('Hubungi koperasi pusat');
        $response = $this->get('/whatsapp/commodity/'.$commodity->id.'?quantity=10')->assertRedirect();
        $url = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/62895333212323?text=', $url);
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        foreach (['Koperasi Pusat KWT', 'Produk: Selada', 'KWT: KWT Sayur Sejahtera', '10 kg', 'Status: Sedang Ditanam', 'Estimasi panen:', 'proses pemesanannya'] as $text) {
            $this->assertStringContainsString($text, $query['text']);
        }
        $this->get('/whatsapp/commodity/'.$commodity->id.'?quantity=0')->assertSessionHasErrors('quantity');
        $commodity->update(['is_active' => false]);
        $this->get('/whatsapp/commodity/'.$commodity->id.'?quantity=10')->assertNotFound();
    }
}
