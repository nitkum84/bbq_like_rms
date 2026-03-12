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
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome');
    }

    return auth()->user()->hasRole('super-admin')
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
})->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return request()->user()->hasRole('super-admin')
            ? redirect()->route('admin.dashboard')
            : view('dashboard');
    })->name('dashboard');

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
