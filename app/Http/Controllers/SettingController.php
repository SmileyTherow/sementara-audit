<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function __invoke(Request $request)
    {
        Gate::authorize('manage-settings');
        $fields = ['cooperative_name' => 'required|string|max:150', 'cooperative_whatsapp' => 'required|regex:/^[0-9+ -]{8,20}$/', 'cooperative_phone' => 'nullable|string|max:30', 'cooperative_email' => 'nullable|email|max:150', 'cooperative_address' => 'nullable|string|max:2000', 'site_name' => 'required|string|max:150', 'site_description' => 'nullable|string|max:2000', 'default_preorder_days' => 'required|integer|between:0,365', 'near_harvest_days' => 'required|integer|between:0,365', 'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'site_favicon' => 'nullable|image|mimes:png|max:1024'];
        if ($request->isMethod('post')) {
            $data = $request->validate($fields);
            foreach (['site_logo', 'site_favicon'] as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $request->file($field)->store('site', 'public');
                } else {
                    unset($data[$field]);
                }
            }
            DB::transaction(function () use ($data) {
                foreach ($data as $key => $value) {
                    $record = Setting::firstOrNew(['key' => $key]);
                    $old = $record->getAttributes();
                    $record->value = $value;
                    $record->save();
                    AuditService::record('settings', $record, $old);
                }
            });

            return back()->with('success', 'Pengaturan berhasil disimpan.');
        }

        return view('admin.settings', compact('fields'));
    }
}
