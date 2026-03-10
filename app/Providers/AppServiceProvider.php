<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SmsService;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(SmsService::class, fn() => new SmsService());
    }
    public function boot(): void {}
}
