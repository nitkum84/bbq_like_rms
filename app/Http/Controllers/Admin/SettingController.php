<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogService;
use App\Services\DynamicMailService;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller {
    public function index(DynamicMailService $mailService, SmsService $smsService): View {
        $settings = WebsiteSetting::pluck('value','key');
        $statuses = [
            'email' => $mailService->isConfigured(),
            'sms' => $smsService->isConfigured(),
        ];

        return view('admin.settings.index', compact('settings', 'statuses'));
    }

    public function update(Request $request, ActivityLogService $activityLogService): RedirectResponse {
        $request->validate([
            'restaurant_name' => 'required|string|max:100',
            'contact_email'   => 'required|email',
            'contact_mobile'  => 'required|string|max:15',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'facebook_url'    => 'nullable|url',
            'instagram_url'   => 'nullable|url',
            'google_maps_url' => 'nullable|url',
            'mail_mailer'     => 'nullable|in:smtp',
            'mail_host'       => 'nullable|string|max:255',
            'mail_port'       => 'nullable|integer|min:1|max:65535',
            'mail_username'   => 'nullable|string|max:255',
            'mail_password'   => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:none,ssl,tls',
            'mail_from_address' => 'nullable|email',
            'mail_from_name'  => 'nullable|string|max:255',
            'sms_user'        => 'nullable|string|max:255',
            'sms_password'    => 'nullable|string|max:255',
            'sms_sender_id'   => 'nullable|string|max:20',
            'sms_base_url'    => 'nullable|url|max:255',
            'sms_delivery_url' => 'nullable|url|max:255',
            'sms_route'       => 'nullable|string|max:20',
            'sms_pe_id'       => 'nullable|string|max:100',
        ]);

        $fields = [
            'restaurant_name','contact_email','contact_mobile','address','booking_note',
            'facebook_url','instagram_url','google_maps_url','maintenance_mode',
            'email_enabled','mail_mailer','mail_host','mail_port','mail_username','mail_password',
            'mail_encryption','mail_from_address','mail_from_name',
            'sms_enabled','sms_user','sms_password','sms_sender_id','sms_base_url',
            'sms_delivery_url','sms_route','sms_pe_id',
        ];
        foreach ($fields as $key) {
            $value = in_array($key, ['maintenance_mode', 'email_enabled', 'sms_enabled'], true)
                ? ($request->boolean($key) ? '1' : '0')
                : $request->input($key);

            if ($key === 'mail_encryption' && $value === 'none') {
                $value = null;
            }

            WebsiteSetting::set($key, $value);
        }
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings','public');
            WebsiteSetting::set('logo', $path);
        }

        $activityLogService->record('settings_updated', null, 'Website and communication settings updated', [
            'fields' => collect($fields)->filter(fn ($field) => $request->has($field))->values()->all(),
        ]);

        return back()->with('success','Settings saved successfully.');
    }

    public function testEmail(Request $request, DynamicMailService $mailService, ActivityLogService $activityLogService): RedirectResponse {
        $validated = $request->validate([
            'test_email_to' => 'required|email',
            'test_email_subject' => 'required|string|max:200',
            'test_email_message' => 'required|string|max:5000',
        ]);

        try {
            $mailService->sendRaw($validated['test_email_to'], $validated['test_email_subject'], $validated['test_email_message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Test email failed: '.$e->getMessage());
        }

        $activityLogService->record('test_email_sent', null, 'Test email sent from settings module', [
            'recipient' => $validated['test_email_to'],
            'subject' => $validated['test_email_subject'],
        ]);

        return back()->with('success', 'Test email sent successfully.');
    }

    public function testSms(Request $request, SmsService $smsService, ActivityLogService $activityLogService): RedirectResponse {
        $validated = $request->validate([
            'test_sms_mobile' => 'required|string|max:15',
            'test_sms_message' => 'required|string|max:500',
        ]);

        try {
            $result = $smsService->send($validated['test_sms_mobile'], $validated['test_sms_message'], 'Trans');

            if (! ($result['success'] ?? false)) {
                return back()->with('error', 'Test SMS failed: '.($result['error'] ?? 'Unknown gateway error.'));
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Test SMS failed: '.$e->getMessage());
        }

        $activityLogService->record('test_sms_sent', null, 'Test SMS sent from settings module', [
            'mobile' => $validated['test_sms_mobile'],
        ]);

        return back()->with('success', 'Test SMS submitted successfully.');
    }
}
