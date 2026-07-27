<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $publishingCost = Setting::get('obituary_publishing_cost', '500');
        return view('admin.settings.index', compact('publishingCost'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'obituary_publishing_cost' => ['required', 'numeric', 'min:0'],
        ]);

        Setting::set('obituary_publishing_cost', $request->input('obituary_publishing_cost'));

        return back()->with('success', 'Site publishing cost updated successfully!');
    }
}
