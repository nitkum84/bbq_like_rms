<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController, MenuCategoryController, MenuItemController,
    TableController, TimeSlotController, PricingRuleController,
    BookingController, DealsBundleController, VoucherController,
    UserController, EnquiryController, BlogController,
    EventsHighlightController, NotificationController, SettingController
};

// ─── Auth Routes ──────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Menu
    Route::resource('menu-categories', MenuCategoryController::class);
    Route::resource('menu-items', MenuItemController::class);
    Route::post('menu-items/{id}/toggle', [MenuItemController::class, 'toggle'])->name('menu-items.toggle');
    Route::post('menu-items/bulk-toggle', [MenuItemController::class, 'bulkToggle'])->name('menu-items.bulk-toggle');

    // Tables
    Route::resource('tables', TableController::class);
    Route::post('tables/{id}/toggle', [TableController::class, 'toggle'])->name('tables.toggle');

    // Time Slots
    Route::resource('time-slots', TimeSlotController::class);

    // Pricing Rules
    Route::resource('pricing-rules', PricingRuleController::class);

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');

    // Deals & Bundles
    Route::resource('deals-bundles', DealsBundleController::class);

    // Vouchers
    Route::resource('vouchers', VoucherController::class);
    Route::post('vouchers/{id}/assign', [VoucherController::class, 'assign'])->name('vouchers.assign');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    // Enquiries
    Route::resource('enquiries', EnquiryController::class);
    Route::post('enquiries/{id}/reply', [EnquiryController::class, 'reply'])->name('enquiries.reply');

    // Blogs
    Route::resource('blogs', BlogController::class);

    // Events & Highlights
    Route::resource('events-highlights', EventsHighlightController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::get('notifications/logs', [NotificationController::class, 'logs'])->name('notifications.logs');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});
