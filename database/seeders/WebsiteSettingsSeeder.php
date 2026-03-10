<?php
namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingsSeeder extends Seeder {
    public function run(): void {
        $settings = [
            ['key' => 'restaurant_name', 'value' => 'My Restaurant',       'group' => 'general'],
            ['key' => 'contact_email',   'value' => 'info@myrestaurant.com','group' => 'general'],
            ['key' => 'contact_mobile',  'value' => '9999999999',           'group' => 'general'],
            ['key' => 'address',         'value' => '123 Main St, City',    'group' => 'general'],
            ['key' => 'booking_note',    'value' => 'We look forward to hosting you!','group' => 'general'],
            ['key' => 'maintenance_mode','value' => '0',                    'group' => 'system'],
        ];
        foreach ($settings as $s) {
            WebsiteSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
