<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting dan ubah jadi array biar gampang dipanggil
        // Contoh hasil: ['school_name' => 'SMA 1 Jakarta', 'academic_year' => '2025/2026']
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Loop semua inputan dan simpan ke database key-value
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan!');
    }
}
