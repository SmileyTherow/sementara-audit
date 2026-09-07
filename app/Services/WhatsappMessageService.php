<?php

namespace App\Services;

use App\Models\Setting;

class WhatsappMessageService
{
    public function link(string $message): string
    {
        $number = preg_replace('/\D/', '', Setting::valueOf('cooperative_whatsapp', ''));
        abort_if(! $number, 422, 'Nomor WhatsApp koperasi belum diatur.');
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    public function buildReadyProductMessage($product, $quantity, $state): string
    {
        return $this->link('Halo Admin '.Setting::valueOf('cooperative_name').",\nSaya tertarik dengan produk berikut:\nProduk: {$product->name}\nKWT: {$product->kwt->name}\nStatus: Ready\nJumlah yang dibutuhkan: {$quantity} {$product->yield_unit}\nKetersediaan saat ini: ±{$state['quantity']} {$product->yield_unit}\nMohon informasi lebih lanjut terkait proses pembelian. Terima kasih.");
    }

    public function buildPreorderMessage($product, $quantity, $state): string
    {
        $cycle = $state['cycle'];

        return $this->link('Halo Admin '.Setting::valueOf('cooperative_name').",\nSaya ingin menanyakan pre-order:\nProduk: {$product->name}\nKWT: {$product->kwt->name}\nEstimasi panen: {$cycle->expected_harvest_date->format('d-m-Y')}\nProyeksi: ±{$cycle->latest_projected_yield} {$cycle->yield_unit}\nJumlah yang dibutuhkan: {$quantity} {$cycle->yield_unit}\nMohon informasi lebih lanjut.");
    }

    public function buildProcessedProductMessage($product, $quantity): string
    {
        return $this->link('Halo Admin '.Setting::valueOf('cooperative_name').",\nSaya tertarik dengan {$product->name} dari {$product->kwt->name}.\nJumlah: {$quantity} {$product->unit}\n{$product->whatsapp_note}\nMohon informasi lebih lanjut.");
    }

    public function buildGrowingProductMessage($product, $quantity, $state): string
    {
        $cycle = $state['cycle'];

        return $this->link('Halo Admin '.Setting::valueOf('cooperative_name').",\n\nSaya ingin menanyakan pemesanan produk berikut:\nProduk: {$product->name}\nKWT: {$product->kwt->name}\nStatus: {$state['status']}\nJumlah yang dibutuhkan: {$quantity} {$product->yield_unit}\nEstimasi panen: {$cycle->expected_harvest_date->format('d-m-Y')}\nProyeksi panen: {$cycle->latest_projected_yield} {$cycle->yield_unit}\n\nApakah produk ini bisa dipesan untuk panen mendatang? Mohon informasi harga, ketersediaan, dan proses pemesanannya.\nTerima kasih.");
    }
}
