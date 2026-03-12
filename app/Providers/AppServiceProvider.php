<?php
namespace App\Providers;

use App\Models\Blog;
use App\Models\Booking;
use App\Models\DealsBundle;
use App\Models\Enquiry;
use App\Models\EventsHighlight;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PricingRule;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Voucher;
use App\Services\ActivityLogService;
use Illuminate\Support\ServiceProvider;
use App\Services\SmsService;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(SmsService::class, fn() => new SmsService());
        $this->app->singleton(ActivityLogService::class, fn() => new ActivityLogService());
    }

    public function boot(): void {
        $this->registerActivityLogging();
    }

    protected function registerActivityLogging(): void {
        foreach ($this->auditableModels() as $modelClass) {
            $modelClass::created(fn ($model) => app(ActivityLogService::class)->recordModelEvent($model, 'created'));
            $modelClass::updated(fn ($model) => app(ActivityLogService::class)->recordModelEvent($model, 'updated'));
            $modelClass::deleted(fn ($model) => app(ActivityLogService::class)->recordModelEvent($model, 'deleted'));
        }
    }

    protected function auditableModels(): array {
        return [
            Booking::class,
            Enquiry::class,
            Voucher::class,
            User::class,
            MenuCategory::class,
            MenuItem::class,
            Table::class,
            TimeSlot::class,
            PricingRule::class,
            DealsBundle::class,
            Blog::class,
            EventsHighlight::class,
        ];
    }
}
