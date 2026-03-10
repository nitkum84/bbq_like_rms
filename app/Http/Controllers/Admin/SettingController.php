<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = WebsiteSetting::pluck('value','key');
        return view('admin.settings.index', compact('settings'));
    }
    public function update(Request $request) {
        $request->validate([
            'restaurant_name' => 'required|string|max:100',
            'contact_email'   => 'required|email',
            'contact_mobile'  => 'required|string|max:15',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $fields = ['restaurant_name','contact_email','contact_mobile','address','booking_note','facebook_url','instagram_url','google_maps_url','maintenance_mode'];
        foreach ($fields as $key) {
            WebsiteSetting::set($key, $request->input($key));
        }
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings','public');
            WebsiteSetting::set('logo', $path);
        }
        return back()->with('success','Settings saved successfully.');
    }
}
