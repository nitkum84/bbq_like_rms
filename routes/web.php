<?php

use App\Http\Controllers\Admin\{
    ActivityLogController,
    BlogController,
    BookingController,
    DashboardController,
    DealsBundleController,
    EnquiryController,
    EventsHighlightController,
    MenuCategoryController,
    MenuItemController,
    NotificationController,
    PricingRuleController,
    SettingController,
    SystemLogController,
    TableController,
    TimeSlotController,
    UserController,
    VoucherController
};
use App\Http\Controllers\Front\ContentController;
use App\Http\Controllers\Front\EnquiryController as FrontEnquiryController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ReservationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/blogs', [ContentController::class, 'blogs'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [ContentController::class, 'blogShow'])->name('blogs.show');
Route::get('/happiness-cards', [ContentController::class, 'happinessCards'])->name('happiness-cards.index');
Route::get('/happiness-cards/{eventsHighlight}', [ContentController::class, 'happinessCardShow'])->name('happiness-cards.show');
Route::get('/deals', [ContentController::class, 'deals'])->name('deals.index');
Route::get('/deals/{dealsBundle}', [ContentController::class, 'dealShow'])->name('deals.show');
Route::get('/catering', [FrontEnquiryController::class, 'create'])->name('enquiries.create');
Route::post('/catering', [FrontEnquiryController::class, 'store'])->name('enquiries.store');
Route::get('/reservations/quote', [ReservationController::class, 'quote'])->name('reservations.quote');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/{confirmationCode}', [ReservationController::class, 'show'])->name('reservations.show');
Route::post('/reservations/{confirmationCode}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
Route::post('/reservations/{confirmationCode}/reschedule', [ReservationController::class, 'reschedule'])->name('reservations.reschedule');
Route::post('/reservations/{confirmationCode}/dashboard-otp', [ReservationController::class, 'verifyDashboardOtp'])->name('reservations.dashboard-otp.verify');
Route::post('/reservations/{confirmationCode}/dashboard-otp/resend', [ReservationController::class, 'resendDashboardOtp'])->name('reservations.dashboard-otp.resend');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard/otp', [ProfileController::class, 'verifyDashboardOtp'])->name('dashboard.otp.verify');
    Route::post('/dashboard/otp/resend', [ProfileController::class, 'resendDashboardOtp'])->name('dashboard.otp.resend');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin-panel')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('menu-categories', MenuCategoryController::class);
    Route::resource('menu-items', MenuItemController::class);
    Route::post('menu-items/{id}/toggle', [MenuItemController::class, 'toggle'])->name('menu-items.toggle');
    Route::post('menu-items/bulk-toggle', [MenuItemController::class, 'bulkToggle'])->name('menu-items.bulk-toggle');

    Route::resource('tables', TableController::class);
    Route::post('tables/{id}/toggle', [TableController::class, 'toggle'])->name('tables.toggle');

    Route::resource('time-slots', TimeSlotController::class);
    Route::resource('pricing-rules', PricingRuleController::class);

    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');

    Route::resource('deals-bundles', DealsBundleController::class);

    Route::resource('vouchers', VoucherController::class);
    Route::post('vouchers/{id}/assign', [VoucherController::class, 'assign'])->name('vouchers.assign');
    Route::post('vouchers/bulk-generate', [VoucherController::class, 'bulkGenerate'])->name('vouchers.bulk-generate');

    Route::resource('users', UserController::class);
    Route::post('users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::post('users/quick-create', [UserController::class, 'quickCreate'])->name('users.quick-create');

    Route::resource('enquiries', EnquiryController::class);
    Route::post('enquiries/{id}/reply', [EnquiryController::class, 'reply'])->name('enquiries.reply');

    Route::resource('blogs', BlogController::class);
    Route::resource('events-highlights', EventsHighlightController::class);

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::get('notifications/logs', [NotificationController::class, 'logs'])->name('notifications.logs');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');

    Route::get('my-profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('my-profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
    Route::post('settings/test-sms', [SettingController::class, 'testSms'])->name('settings.test-sms');
});
