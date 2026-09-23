<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\ProcessedProduct;
use App\Models\Setting;

class WhatsappMessageService
{
    public function link(string $message): string
    {
        $number = Setting::where('key', 'whatsapp_number')->value('value');

        $number = preg_replace('/\D/', '', (string) $number);

        abort_if(
            ! $number,
            422,
            'Nomor WhatsApp koperasi belum diatur.'
        );

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return 'https://wa.me/' . $number
            . '?text=' . rawurlencode($message);
    }

    public function buildCommodityMessage(
        Commodity $commodity,
        $quantity,
        array $state
    ): string {
        $cycle = $state['cycle'];
        $kwt = $cycle?->kwt;

        $message = "Halo Koperasi,\n";
        $message .= "Saya tertarik dengan komoditas berikut:\n";
        $message .= "Tanaman: {$commodity->name}\n";
        $message .= "KWT: " . ($kwt?->name ?? '-') . "\n";
        $message .= "Status: {$state['status']}\n";
        $message .= "Jumlah: {$quantity} {$commodity->default_unit}\n";

        if ($cycle?->estimated_harvest_date) {
            $message .= 'Estimasi panen: '
                . $cycle->estimated_harvest_date->format('d-m-Y')
                . "\n";
        }

        if ($state['quantity'] > 0) {
            $message .= "Ketersediaan: {$state['quantity']} "
                . "{$commodity->default_unit}\n";
        }

        $message .= "Mohon informasi lebih lanjut. Terima kasih.";

        return $this->link($message);
    }

    public function buildProcessedProductMessage(
        ProcessedProduct $product,
        $quantity
    ): string {
        $message = "Halo Koperasi,\n";
        $message .= "Saya tertarik dengan produk berikut:\n";
        $message .= "Produk: {$product->name}\n";
        $message .= "KWT: " . ($product->kwt?->name ?? '-') . "\n";
        $message .= "Jumlah: {$quantity} {$product->unit}\n";

        if ($product->price !== null) {
            $message .= "Harga: Rp "
                . number_format($product->price, 0, ',', '.')
                . " per {$product->unit}\n";
        }

        $message .= "Mohon informasi lebih lanjut. Terima kasih.";

        return $this->link($message);
    }
}
