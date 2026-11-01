<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.index', [
            'settings' => Setting::pluck('value', 'key')->all(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_open' => 'required|string|max:10',
            'business_close' => 'required|string|max:10',
            'break_start' => 'nullable|string|max:10',
            'break_end' => 'nullable|string|max:10',
            'auto_skip_minutes' => 'required|integer|min:1|max:120',
            'daily_token_limit' => 'required|integer|min:1|max:10000',
            'notify_before_turn' => 'required|integer|min:1|max:20',
            'show_public_display' => 'nullable|boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $key === 'show_public_display' ? ($value ? '1' : '0') : $value);
        }

        return redirect()->route('admin.settings.business')->with('success', 'Business settings saved.');
    }

    public function editGeneral()
    {
        return view('admin.settings.general', [
            'settings' => Setting::pluck('value', 'key')->all(),
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'token_prefix' => 'nullable|string|max:10',
            'show_public_display' => 'nullable|boolean',
        ]);

        Setting::set('token_prefix', $data['token_prefix'] ?? '');
        Setting::set('show_public_display', ! empty($data['show_public_display']) ? '1' : '0');

        return redirect()->route('admin.settings.general')->with('success', 'Settings saved.');
    }
}
