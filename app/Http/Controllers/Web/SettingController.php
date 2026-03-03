<?php

namespace App\Http\Controllers\Web;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * GET /admin/settings — halaman kelola settings.
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings', compact('settings'));
    }

    /**
     * PUT /admin/settings — bulk update settings.
     */
    public function update(Request $request)
    {
        $values = $request->input('settings', []);

        foreach ($values as $id => $value) {
            $setting = Setting::find($id);
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
