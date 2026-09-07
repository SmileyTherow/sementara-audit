<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Setting;

class ProductAvailabilityService
{
    public function state(Commodity $commodity): array
    {
        $availability = $commodity->availabilities->sortByDesc('id')->first();
        $quantity = $availability && in_array($availability->availability_status, ['available', 'limited']) ? (float) $availability->available_quantity : 0;
        $active = $commodity->cycles->whereIn('status', ['seedling', 'growing', 'near_harvest']);
        $cycle = $active->sortBy(fn ($c) => ($c->preorder_enabled ? '0' : '1').$c->expected_harvest_date->format('Y-m-d'))->first();
        $status = $availability ? 'Habis' : 'Tidak Aktif';
        if ($cycle) {
            $near = $cycle->status === 'near_harvest' || $cycle->expected_harvest_date->lte(now()->addDays((int) Setting::valueOf('near_harvest_days', 7)));
            $status = $cycle->preorder_enabled ? 'Pre-Order' : ($near ? 'Segera Panen' : 'Sedang Ditanam');
        }
        if ($quantity > 0) {
            $status = 'Ready';
        }
        if (! $commodity->is_active || $commodity->kwt?->status !== 'active') {
            $status = 'Tidak Aktif';
        }

        return compact('status', 'quantity', 'cycle', 'availability');
    }
}
