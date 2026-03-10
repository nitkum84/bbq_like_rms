<?php
namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;

class MenuDailyReset extends Command {
    protected $signature   = 'menu:daily-reset';
    protected $description = 'Reset all menu items to available at midnight';

    public function handle(): void {
        $count = MenuItem::where('is_available', false)->count();
        MenuItem::query()->update(['is_available' => true]);
        $this->info("Menu reset: {$count} items made available.");
    }
}
