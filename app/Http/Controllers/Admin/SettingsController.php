<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = SchoolSetting::current();

        return view('admin.pengaturan.edit', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'check_in_start' => ['required', 'date_format:H:i'],
            'check_in_end' => ['required', 'date_format:H:i', 'after:check_in_start'],
            'late_threshold' => ['required', 'date_format:H:i'],
            'check_out_start' => ['required', 'date_format:H:i'],
            'check_out_end' => ['required', 'date_format:H:i', 'after:check_out_start'],
        ]);

        $settings = SchoolSetting::current();
        $settings->fill([
            'school_name' => $data['school_name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius_meters' => $data['radius_meters'],
            'check_in_start' => $data['check_in_start'].':00',
            'check_in_end' => $data['check_in_end'].':00',
            'late_threshold' => $data['late_threshold'].':00',
            'check_out_start' => $data['check_out_start'].':00',
            'check_out_end' => $data['check_out_end'].':00',
        ]);
        $settings->updated_at = now();
        $settings->save();

        return redirect()->route('admin.sekolah.edit')
            ->with('status', 'Data sekolah berhasil disimpan.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        $settings = SchoolSetting::current();

        if ($settings->logo) {
            Storage::disk('public')->delete($settings->logo);
        }

        $settings->logo = $request->file('logo')->store('logo', 'public');
        $settings->updated_at = now();
        $settings->save();

        return redirect()->route('admin.sekolah.edit')
            ->with('status', 'Logo sekolah berhasil diperbarui.');
    }
}
