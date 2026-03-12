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
            ['key' => 'email_enabled',   'value' => '0',                    'group' => 'communication'],
            ['key' => 'mail_mailer',     'value' => 'smtp',                 'group' => 'communication'],
            ['key' => 'mail_host',       'value' => '',                     'group' => 'communication'],
            ['key' => 'mail_port',       'value' => '587',                  'group' => 'communication'],
            ['key' => 'mail_username',   'value' => '',                     'group' => 'communication'],
            ['key' => 'mail_password',   'value' => '',                     'group' => 'communication'],
            ['key' => 'mail_encryption', 'value' => 'tls',                  'group' => 'communication'],
            ['key' => 'mail_from_address', 'value' => '',                   'group' => 'communication'],
            ['key' => 'mail_from_name',  'value' => 'My Restaurant',        'group' => 'communication'],
            ['key' => 'sms_enabled',     'value' => '0',                    'group' => 'communication'],
            ['key' => 'sms_user',        'value' => '',                     'group' => 'communication'],
            ['key' => 'sms_password',    'value' => '',                     'group' => 'communication'],
            ['key' => 'sms_sender_id',   'value' => '',                     'group' => 'communication'],
            ['key' => 'sms_base_url',    'value' => '',                     'group' => 'communication'],
            ['key' => 'sms_delivery_url','value' => '',                     'group' => 'communication'],
            ['key' => 'sms_route',       'value' => '4',                    'group' => 'communication'],
            ['key' => 'sms_pe_id',       'value' => '',                     'group' => 'communication'],
        ];
        foreach ($settings as $s) {
            WebsiteSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
